import 'package:flutter/material.dart';
import '../services/produto_service.dart';
import '../models/produto.dart';
import 'package:lojajaqueline/app_drawer.dart';

class CalcadosScreen extends StatefulWidget {
  const CalcadosScreen({super.key});

  @override
  State<CalcadosScreen> createState() => _CalcadosScreenState();
}

class _CalcadosScreenState extends State<CalcadosScreen> {
  final ProdutoService _produtoService = ProdutoService();
  late Future<List<Produto>> _calcadosFuture;
  String? _subcategoria;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    _subcategoria = ModalRoute.of(context)?.settings.arguments as String?;
    _calcadosFuture = _produtoService.getProdutos(
      categoria: 'Calçados',
      subcategoria: _subcategoria,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Calçados - ${_subcategoria ?? "Todos"}')),
      drawer: const AppDrawer(),
      body: FutureBuilder<List<Produto>>(
        future: _calcadosFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(
              child: Text('Erro ao carregar os calçados: ${snapshot.error}'),
            );
          } else if (snapshot.hasData) {
            final calcados = snapshot.data!;
            return ListView.builder(
              itemCount: calcados.length,
              itemBuilder: (context, index) {
                final produto = calcados[index];
                return Card(
                  margin: const EdgeInsets.all(8.0),
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          produto.nome,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        if (produto
                            .imagens
                            .isNotEmpty) // Verifica se há imagens
                          SizedBox(
                            height: 100,
                            child: ListView.builder(
                              scrollDirection: Axis.horizontal,
                              itemCount: produto.imagens.length,
                              itemBuilder: (context, imageIndex) {
                                final imageUrl = produto.imagens[imageIndex];
                                return Padding(
                                  padding: const EdgeInsets.only(right: 8.0),
                                  child: SizedBox(
                                    width: 100,
                                    child: Image.network(
                                      imageUrl,
                                      fit: BoxFit.cover,
                                      errorBuilder: (
                                        context,
                                        error,
                                        stackTrace,
                                      ) {
                                        return const Text('Erro na imagem');
                                      },
                                    ),
                                  ),
                                );
                              },
                            ),
                          )
                        else
                          const Text('Sem imagens disponíveis'),
                        Text(produto.descricao),
                        Text('Preço: R\$ ${produto.preco.toStringAsFixed(2)}'),
                        // Outros detalhes conforme necessário
                      ],
                    ),
                  ),
                );
              },
            );
          } else {
            return const Center(
              child: Text('Nenhum calçado encontrado nesta categoria.'),
            );
          }
        },
      ),
    );
  }
}
