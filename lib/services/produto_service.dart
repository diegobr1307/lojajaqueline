import 'package:cloud_firestore/cloud_firestore.dart';
import '../models/produto.dart';

class ProdutoService {
  final CollectionReference _produtosCollection = FirebaseFirestore.instance
      .collection('produtos');

  Future<List<Produto>> getProdutos({
    String? categoria,
    String? subcategoria,
  }) async {
    Query query = _produtosCollection;

    if (categoria != null) {
      query = query.where('categoria', isEqualTo: categoria);
    }

    if (subcategoria != null) {
      query = query.where('subcategoria', isEqualTo: subcategoria);
    }

    QuerySnapshot snapshot = await query.get();
    return snapshot.docs.map((doc) {
      return Produto.fromFirestore(doc.data() as Map<String, dynamic>, doc.id);
    }).toList();
  }

  Future<List<Produto>> getBrinquedosPorSubcategoria(
    String subcategoria,
  ) async {
    return getProdutos(categoria: 'Brinquedos', subcategoria: subcategoria);
  }

  Future<List<Produto>> getProdutosEmPromocao() async {
    QuerySnapshot snapshot =
        await _produtosCollection.where('emPromocao', isEqualTo: true).get();
    return snapshot.docs.map((doc) {
      return Produto.fromFirestore(doc.data() as Map<String, dynamic>, doc.id);
    }).toList();
  }
}
