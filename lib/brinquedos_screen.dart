import 'package:flutter/material.dart';
import '../services/produto_service.dart';
import '../models/produto.dart';
import 'package:lojajaqueline/app_drawer.dart';

class BrinquedosScreen extends StatefulWidget {
  const BrinquedosScreen({super.key});

  @override
  State<BrinquedosScreen> createState() => _BrinquedosScreenState();
}

class _BrinquedosScreenState extends State<BrinquedosScreen> {
  final ProdutoService _produtoService = ProdutoService();
  late Future<List<Produto>> _brinquedosFuture;
  String? _subcategoria;

  @override
  void initState() {
    super.initState();
    _subcategoria = ModalRoute.of(context)?.settings.arguments as String?;
    _brinquedosFuture = _loadBrinquedos();
  }

  Future<List<Produto>> _loadBrinquedos() async {
    if (_subcategoria != null && _subcategoria!.isNotEmpty) {
      return await _produtoService.getBrinquedosPorSubcategoria(_subcategoria!);
    } else {
      return await _produtoService.getProdutos(categoria: 'Brinquedos');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          _subcategoria != null && _subcategoria!.isNotEmpty
              ? 'Brinquedos - $_subcategoria'
              : 'Brinquedos',
        ),
      ),
      drawer: const AppDrawer(),
      body: FutureBuilder<List<Produto>>(
        future: _brinquedosFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(
              child: Text('Erro ao carregar os brinquedos: ${snapshot.error}'),
            );
          } else if (snapshot.hasData) {
            final brinquedos = snapshot.data!;
            if (brinquedos.isEmpty) {
              return Center(
                // Removi o 'const' aqui
                child: Text(
                  'Nenhum brinquedo encontrado${_subcategoria != null && _subcategoria!.isNotEmpty ? ' na subcategoria $_subcategoria' : ''}.',
                ),
              );
            }
            return ListView.builder(
              itemCount: brinquedos.length,
              itemBuilder: (context, index) {
                final produto = brinquedos[index];
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
                      ],
                    ),
                  ),
                );
              },
            );
          } else {
            return const Center(child: Text('Nenhum brinquedo encontrado.'));
          }
        },
      ),
    );
  }
}
