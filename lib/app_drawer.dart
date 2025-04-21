// lib/app_drawer.dart
import 'package:flutter/material.dart';

class AppDrawer extends StatelessWidget {
  const AppDrawer({super.key});

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: <Widget>[
          const DrawerHeader(
            decoration: BoxDecoration(color: Color(0xFF993399)),
            child: Text(
              'Menu',
              style: TextStyle(color: Colors.white, fontSize: 24),
            ),
          ),
          ListTile(
            leading: const Icon(Icons.home, color: Color(0xFF993399)),
            title: const Text(
              'Home',
              style: TextStyle(color: Color(0xFF993399)),
            ),
            onTap: () {
              Navigator.pop(context);
              Navigator.pushReplacementNamed(context, '/');
            },
          ),
          ExpansionTile(
            title: const Text(
              'Calçados',
              style: TextStyle(color: Color(0xFF993399)),
            ),
            leading: const Icon(Icons.local_mall, color: Color(0xFF993399)),
            children: <Widget>[
              ListTile(
                title: const Text(
                  'Adulto',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/calcados',
                    arguments: 'Adulto',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Infantil',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/calcados',
                    arguments: 'Infantil',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Tênis',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(context, '/calcados', arguments: 'Tenis');
                },
              ),
              ListTile(
                title: const Text(
                  'Sandálias',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/calcados',
                    arguments: 'Sandalias',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Botas',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(context, '/calcados', arguments: 'Botas');
                },
              ),
              ListTile(
                title: const Text(
                  'Chinelos',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/calcados',
                    arguments: 'Chinelos',
                  );
                },
              ),
            ],
          ),
          ExpansionTile(
            title: const Text(
              'Brinquedos',
              style: TextStyle(color: Color(0xFF993399)),
            ),
            leading: const Icon(Icons.toys, color: Color(0xFF993399)),
            children: <Widget>[
              ListTile(
                title: const Text(
                  'Boneca',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Boneca',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Carrinho de Controle Remoto',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Carrinho de Controle Remoto',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Carrinho',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Carrinho',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Bonecos',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Bonecos',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Mordedor',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Mordedor',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Patins',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Patins',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Quebra Cabeça',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Quebra Cabeça',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Jogos',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Jogos',
                  );
                },
              ),
              ListTile(
                title: const Text(
                  'Bola',
                  style: TextStyle(color: Color(0xFF993399)),
                ),
                onTap: () {
                  Navigator.pop(context);
                  Navigator.pushNamed(
                    context,
                    '/brinquedos',
                    arguments: 'Bola',
                  );
                },
              ),
            ],
          ),
          ListTile(
            leading: const Icon(Icons.build, color: Color(0xFF993399)),
            title: const Text(
              'Utilidades',
              style: TextStyle(color: Color(0xFF993399)),
            ),
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/utilidades');
            },
          ),
          ListTile(
            leading: const Icon(Icons.local_offer, color: Color(0xFF993399)),
            title: const Text(
              'Promoções',
              style: TextStyle(color: Color(0xFF993399)),
            ),
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/promocoes');
            },
          ),
          ListTile(
            leading: const Icon(Icons.map, color: Color(0xFF993399)),
            title: const Text(
              'Como Chegar',
              style: TextStyle(color: Color(0xFF993399)),
            ),
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/como_chegar');
            },
          ),
        ],
      ),
    );
  }
}
