import 'package:flutter/material.dart';
import '../services/produto_service.dart';
import '../models/produto.dart';
import 'package:lojajaqueline/app_drawer.dart';

class UtilidadesScreen extends StatefulWidget {
  const UtilidadesScreen({super.key});

  @override
  State<UtilidadesScreen> createState() => _UtilidadesScreenState();
}

class _UtilidadesScreenState extends State<UtilidadesScreen> {
  final ProdutoService _produtoService = ProdutoService();
  late Future<List<Produto>> _utilidadesFuture;

  @override
  void initState() {
    super.initState();
    _utilidadesFuture = _produtoService.getProdutos(categoria: 'Utilidades');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Utilidades')),
      drawer: const AppDrawer(),
      body: FutureBuilder<List<Produto>>(
        future: _utilidadesFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(
              child: Text(
                'Erro ao carregar os produtos de utilidades: ${snapshot.error}',
              ),
            );
          } else if (snapshot.hasData) {
            final utilidades = snapshot.data!;
            return ListView.builder(
              itemCount: utilidades.length,
              itemBuilder: (context, index) {
                final produto = utilidades[index];
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
                        Text(produto.descricao),
                        Text('Preço: R\$ ${produto.preco.toStringAsFixed(2)}'),
                        // Aqui você pode adicionar a exibição da imagem
                      ],
                    ),
                  ),
                );
              },
            );
          } else {
            return const Center(
              child: Text('Nenhum produto de utilidade encontrado.'),
            );
          }
        },
      ),
    );
  }
}
