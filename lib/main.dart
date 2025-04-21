// lib/main.dart
import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'firebase_options.dart';
import './app_drawer.dart';
import './services/produto_service.dart';
import './models/produto.dart';
import './calcados_screen.dart';
import './brinquedos_screen.dart';
import './utilidades_screen.dart';
import './promocoes_screen.dart';
import './como_chegar_screen.dart';

MaterialColor createMaterialColor(Color color) {
  List strengths = <double>[.05];
  Map<int, Color> swatch = <int, Color>{};
  final int r = color.red, g = color.green, b = color.blue;

  for (int i = 1; i < 10; i++) {
    strengths.add(0.1 * i);
  }
  strengths.forEach((strength) {
    final double ds = 0.5 - strength;
    swatch[(strength * 1000).round()] = Color.fromRGBO(
      r + ((ds < 0 ? r : (255 - r)) * ds).round(),
      g + ((ds < 0 ? g : (255 - g)) * ds).round(),
      b + ((ds < 0 ? b : (255 - b)) * ds).round(),
      1,
    );
  });
  return MaterialColor(color.value, swatch);
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Jaqueline Andrade',
      theme: ThemeData(
        primarySwatch: createMaterialColor(const Color(0xFF993399)),
      ),
      home: const HomePage(),
      routes: {
        '/calcados': (context) => const CalcadosScreen(),
        '/brinquedos': (context) => const BrinquedosScreen(),
        '/utilidades': (context) => const UtilidadesScreen(),
        '/promocoes': (context) => const PromocoesScreen(),
        '/como_chegar': (context) => const ComoChegarScreen(),
      },
    );
  }
}

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final ProdutoService _produtoService = ProdutoService();
  late Future<List<Produto>> _todosProdutosFuture;
  List<Produto> _listaProdutos = [];
  String _searchText = '';

  @override
  void initState() {
    super.initState();
    _todosProdutosFuture = _produtoService.getProdutos();
    _todosProdutosFuture.then((produtos) {
      setState(() {
        _listaProdutos = produtos;
      });
    });
  }

  // Filtra a lista de produtos com base no texto de busca
  List<Produto> get _produtosFiltrados {
    if (_searchText.isEmpty) {
      return []; // Não mostrar nada se a pesquisa estiver vazia
    }
    return _listaProdutos
        .where(
          (produto) =>
              produto.nome.toLowerCase().contains(_searchText.toLowerCase()) ||
              produto.descricao.toLowerCase().contains(
                _searchText.toLowerCase(),
              ),
        )
        .toList();
  }

  void _onSearchChanged(String value) {
    setState(() {
      _searchText = value;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Image.network('https://i.ibb.co/TM6LgQK/logo.png', height: 30),
            const SizedBox(width: 8),
            const Text(
              'Jaqueline Andrade',
              style: TextStyle(color: Colors.white),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF993399),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(60.0),
          child: Padding(
            padding: const EdgeInsets.all(8.0),
            child: TextField(
              onChanged: _onSearchChanged,
              style: const TextStyle(color: Colors.black),
              decoration: InputDecoration(
                filled: true,
                fillColor: Colors.white,
                hintText: 'Buscar produtos...',
                hintStyle: const TextStyle(color: Colors.grey),
                prefixIcon: const Icon(Icons.search, color: Colors.grey),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10.0),
                  borderSide: BorderSide.none,
                ),
              ),
            ),
          ),
        ),
      ),
      drawer: const AppDrawer(),
      backgroundColor: const Color(0xFF993399),
      body:
          _listaProdutos.isEmpty
              ? const Center(
                child: CircularProgressIndicator(color: Colors.white),
              )
              : _searchText.isEmpty
              ? const Center(
                child: Text(
                  'Digite algo para buscar os produtos.',
                  style: TextStyle(color: Colors.white, fontSize: 16),
                  textAlign: TextAlign.center,
                ),
              )
              : _produtosFiltrados.isEmpty
              ? const Center(
                child: Text(
                  'Nenhum produto encontrado.',
                  style: TextStyle(color: Colors.white, fontSize: 16),
                  textAlign: TextAlign.center,
                ),
              )
              : ListView.builder(
                itemCount: _produtosFiltrados.length,
                itemBuilder: (context, index) {
                  final produto = _produtosFiltrados[index];
                  return Card(
                    margin: const EdgeInsets.all(8.0),
                    color: Colors.white,
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
                              color: Colors.black,
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
                                          return const Text(
                                            'Erro na imagem',
                                            style: TextStyle(
                                              color: Colors.black,
                                            ),
                                          );
                                        },
                                      ),
                                    ),
                                  );
                                },
                              ),
                            )
                          else
                            const Text(
                              'Sem imagens disponíveis',
                              style: TextStyle(color: Colors.black),
                            ),
                          Text(
                            produto.descricao,
                            style: const TextStyle(color: Colors.black),
                          ),
                          Text(
                            'Preço: R\$ ${produto.preco.toStringAsFixed(2)}',
                            style: const TextStyle(color: Colors.black),
                          ),
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
              ),
    );
  }
}
