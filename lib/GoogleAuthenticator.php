<?php
require_once __DIR__ . '/../auth.php';
/**
 * GoogleAuthenticator.php - TOTP (Google/Microsoft Authenticator) sem Composer
 * - Secret Base32 padrão (A-Z2-7)
 * - QR via api.qrserver.com
 * - PHP 8+ compatível
 */
class GoogleAuthenticator
{
    private int $codeLength = 6;

    public function createSecret(int $length = 16): string
    {
        // Base32 padrão (RFC 4648) usado por Authenticator
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }
        return $secret;
    }

    public function getQRCodeGoogleUrl(string $label, string $secret, string $issuer = 'LojaJaqueline'): string
    {
        $otpAuth = 'otpauth://totp/' . $label . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($otpAuth);
    }

    public function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $code = preg_replace('/\D+/', '', $code);
        if ($code === '' || strlen($code) !== $this->codeLength) {
            return false;
        }

        $timeSlice = (int)floor(time() / 30);
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calc = $this->getCode($secret, $timeSlice + $i);
            if (hash_equals($calc, $code)) {
                return true;
            }
        }
        return false;
    }

    // PUBLICO para permitir debug no login.php
    public function getCode(string $secret, ?int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = (int)floor(time() / 30);
        }

        $key = $this->base32Decode($secret);
        if ($key === '') {
            return str_repeat('0', $this->codeLength);
        }

        // contador de 8 bytes (big-endian): high=0, low=timeSlice
        $time = pack('N2', 0, $timeSlice);

        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hash[19]) & 0x0F;

        $binary =
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF);

        $otp = $binary % (10 ** $this->codeLength);
        return str_pad((string)$otp, $this->codeLength, '0', STR_PAD_LEFT);
    }

    public function setCodeLength(int $length): self
    {
        $this->codeLength = $length;
        return $this;
    }

    private function base32Decode(string $secret): string
    {
        $secret = strtoupper($secret);
        $secret = preg_replace('/[^A-Z2-7]/', '', $secret);

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $map = [];
        for ($i = 0; $i < 32; $i++) {
            $map[$alphabet[$i]] = $i;
        }

        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        $len = strlen($secret);
        for ($i = 0; $i < $len; $i++) {
            $ch = $secret[$i];
            if (!isset($map[$ch])) continue;

            $buffer = ($buffer << 5) | $map[$ch];
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $result;
    }
}
