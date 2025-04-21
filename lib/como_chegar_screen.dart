import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:lojajaqueline/app_drawer.dart';

class ComoChegarScreen extends StatelessWidget {
  const ComoChegarScreen({super.key});

  final String endereco =
      "Avenida Senador Melor Viana, 870 - Goias, Araguari - MG, 38442-192";
  final String mapsUrl =
      "https://maps.app.goo.gl/qcRpvDYNWXMWSyDU7"; // Seu link do Google Maps

  _launchURL() async {
    Uri uri = Uri.parse(mapsUrl);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    } else {
      throw 'Não foi possível abrir o link $mapsUrl';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Como Chegar', style: TextStyle(color: Colors.white)),
        backgroundColor: const Color(0xFF993399),
      ),
      drawer: const AppDrawer(),
      backgroundColor: const Color(0xFF993399),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: <Widget>[
              Text(
                'Nosso Endereço:',
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 10),
              Text(
                endereco,
                style: const TextStyle(fontSize: 16, color: Colors.white),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: _launchURL,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white, // Cor do botão
                  foregroundColor: const Color(0xFF993399), // Cor do texto
                ),
                child: const Text('Abrir no Google Maps'),
              ),
              const SizedBox(height: 10),
              const Text(
                '(Clique no botão acima para abrir o endereço no Google Maps)',
                style: TextStyle(fontSize: 14, color: Colors.white70),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
