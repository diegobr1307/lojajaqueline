import 'package:flutter/material.dart';
import '../services/produto_service.dart';
import '../models/produto.dart';
import 'package:lojajaqueline/app_drawer.dart';

class PromocoesScreen extends StatefulWidget {
  const PromocoesScreen({super.key});

  @override
  State<PromocoesScreen> createState() => _PromocoesScreenState();
}

class _PromocoesScreenState extends State<PromocoesScreen> {
  final ProdutoService _produtoService = ProdutoService();
  late Future<List<Produto>> _promocoesFuture;

  @override
  void initState() {
    super.initState();
    _promocoesFuture = _produtoService.getProdutosEmPromocao();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Promoções')),
      drawer: const AppDrawer(),
      body: FutureBuilder<List<Produto>>(
        future: _promocoesFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(
              child: Text('Erro ao carregar as promoções: ${snapshot.error}'),
            );
          } else if (snapshot.hasData) {
            final promocoes = snapshot.data!;
            return ListView.builder(
              itemCount: promocoes.length,
              itemBuilder: (context, index) {
                final produto = promocoes[index];
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
                        if (produto.imagens.isNotEmpty)
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
                        if (produto.emPromocao &&
                            produto.precoPromocional != null)
                          Text(
                            'Promoção: R\$ ${produto.precoPromocional!.toStringAsFixed(2)}',
                            style: const TextStyle(color: Colors.red),
                          ),
                        // Aqui as imagens serão exibidas
                      ],
                    ),
                  ),
                );
              },
            );
          } else {
            return const Center(child: Text('Nenhuma promoção encontrada.'));
          }
        },
      ),
    );
  }
}
