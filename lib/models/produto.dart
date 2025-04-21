class Produto {
  final String id;
  final String nome;
  final String descricao;
  final double preco;
  final List<String> imagens;
  final String categoria;
  final String? subcategoria;
  final bool emPromocao;
  final double? precoPromocional;

  Produto({
    required this.id,
    required this.nome,
    required this.descricao,
    required this.preco,
    required this.imagens,
    required this.categoria,
    this.subcategoria,
    this.emPromocao = false,
    this.precoPromocional,
  });

  factory Produto.fromFirestore(Map<String, dynamic> data, String id) {
    return Produto(
      id: id,
      nome: data['nome'] ?? '',
      descricao: data['descricao'] ?? '',
      preco: (data['preco'] ?? 0.0).toDouble(),
      imagens: List<String>.from(data['imagens'] ?? []),
      categoria: data['categoria'] ?? '',
      subcategoria: data['subcategoria'],
      emPromocao: data['emPromocao'] ?? false,
      precoPromocional: (data['precoPromocional'] ?? 0.0).toDouble(),
    );
  }
}
