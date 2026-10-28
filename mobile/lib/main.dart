import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import 'api_client.dart';

void main() => runApp(const MiniMarketApp());

class MiniMarketApp extends StatefulWidget {
  const MiniMarketApp({super.key});

  @override
  State<MiniMarketApp> createState() => _MiniMarketAppState();
}

class _MiniMarketAppState extends State<MiniMarketApp> {
  static const _storage = FlutterSecureStorage();
  static const _tokenKey = 'mobile_api_token';
  static const _userKey = 'mobile_user';

  ApiClient? _api;
  MobileUser? _user;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _restoreSession();
  }

  Future<void> _restoreSession() async {
    final token = await _storage.read(key: _tokenKey);
    final userJson = await _storage.read(key: _userKey);
    if (token != null && userJson != null) {
      try {
        final decoded = jsonDecode(userJson);
        if (decoded is! Map<String, dynamic>) {
          throw const FormatException('Data akun tersimpan tidak valid.');
        }
        _user = MobileUser.fromJson(decoded);
        _api = ApiClient(accessToken: token);
      } on FormatException {
        await _storage.delete(key: _tokenKey);
        await _storage.delete(key: _userKey);
      }
    }
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _signIn(String email, String password) async {
    final loginClient = ApiClient();
    try {
      final session = await loginClient.login(email, password);
      final api = ApiClient(accessToken: session.token);
      final user = session.user;
      await _storage.write(key: _tokenKey, value: session.token);
      await _storage.write(key: _userKey, value: jsonEncode(user.toJson()));
      if (!mounted) {
        api.close();
        return;
      }
      setState(() {
        _api = api;
        _user = user;
      });
    } finally {
      loginClient.close();
    }
  }

  Future<void> _signOut() async {
    final api = _api;
    if (api != null) {
      try {
        await api.logout();
      } on ApiException {
        // Local credentials are still removed when the server is unreachable.
      } on FormatException {
        // Local credentials are still removed if the server response is invalid.
      } finally {
        api.close();
      }
    }
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
    if (mounted) {
      setState(() {
        _api = null;
        _user = null;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Minimarket POS',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFFB45309),
          brightness: Brightness.light,
        ),
        useMaterial3: true,
        scaffoldBackgroundColor: const Color(0xFFF8FAFC),
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(14),
            borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(14),
            borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
          ),
        ),
      ),
      home: _loading
          ? const Scaffold(body: Center(child: CircularProgressIndicator()))
          : _user == null || _api == null
          ? LoginScreen(onSignIn: _signIn)
          : RestockHome(api: _api!, user: _user!, onSignOut: _signOut),
    );
  }
}

class LoginScreen extends StatefulWidget {
  const LoginScreen({required this.onSignIn, super.key});

  final Future<void> Function(String email, String password) onSignIn;

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _password = TextEditingController();
  String? _error;
  bool _busy = false;
  bool _obscurePassword = true;

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      await widget.onSignIn(_email.text.trim(), _password.text);
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } on FormatException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 430),
              child: Card(
                elevation: 0,
                color: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(24),
                  side: const BorderSide(color: Color(0xFFE2E8F0)),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const Icon(
                          Icons.storefront_rounded,
                          size: 44,
                          color: Color(0xFFB45309),
                        ),
                        const SizedBox(height: 16),
                        Text(
                          'Minimarket POS',
                          textAlign: TextAlign.center,
                          style: Theme.of(context).textTheme.headlineSmall
                              ?.copyWith(fontWeight: FontWeight.w900),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Masuk sebagai admin untuk mengelola stok.',
                          textAlign: TextAlign.center,
                          style: Theme.of(context).textTheme.bodyMedium
                              ?.copyWith(color: Colors.blueGrey),
                        ),
                        const SizedBox(height: 24),
                        if (_error != null) ...[
                          _MessageBanner(message: _error!),
                          const SizedBox(height: 16),
                        ],
                        TextFormField(
                          controller: _email,
                          keyboardType: TextInputType.emailAddress,
                          textInputAction: TextInputAction.next,
                          autofillHints: const [AutofillHints.username],
                          decoration: const InputDecoration(
                            labelText: 'Email',
                            prefixIcon: Icon(Icons.email_outlined),
                          ),
                          validator: (value) =>
                              value == null || !value.trim().contains('@')
                              ? 'Masukkan alamat email yang valid.'
                              : null,
                        ),
                        const SizedBox(height: 14),
                        TextFormField(
                          controller: _password,
                          obscureText: _obscurePassword,
                          textInputAction: TextInputAction.done,
                          autofillHints: const [AutofillHints.password],
                          onFieldSubmitted: (_) => _submit(),
                          decoration: InputDecoration(
                            labelText: 'Kata sandi',
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              onPressed: () => setState(
                                () => _obscurePassword = !_obscurePassword,
                              ),
                              icon: Icon(
                                _obscurePassword
                                    ? Icons.visibility_outlined
                                    : Icons.visibility_off_outlined,
                              ),
                            ),
                          ),
                          validator: (value) => value == null || value.isEmpty
                              ? 'Kata sandi wajib diisi.'
                              : null,
                        ),
                        const SizedBox(height: 20),
                        FilledButton.icon(
                          onPressed: _busy ? null : _submit,
                          icon: _busy
                              ? const SizedBox.square(
                                  dimension: 18,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                  ),
                                )
                              : const Icon(Icons.login),
                          label: Text(_busy ? 'Menghubungkan...' : 'Masuk'),
                          style: FilledButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 15),
                          ),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Server: $apiBaseUrl',
                          textAlign: TextAlign.center,
                          style: Theme.of(context).textTheme.labelSmall
                              ?.copyWith(color: Colors.blueGrey),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class RestockHome extends StatefulWidget {
  const RestockHome({
    required this.api,
    required this.user,
    required this.onSignOut,
    super.key,
  });

  final ApiClient api;
  final MobileUser user;
  final Future<void> Function() onSignOut;

  @override
  State<RestockHome> createState() => _RestockHomeState();
}

class _RestockHomeState extends State<RestockHome> {
  int _tab = 0;
  bool _loading = true;
  bool _signingOut = false;
  String? _loadError;
  List<InventoryProduct> _products = const [];
  List<RestockRecord> _restocks = const [];
  int _lowStockThreshold = 5;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() {
      _loading = true;
      _loadError = null;
    });
    try {
      final inventory = await widget.api.products();
      final restocks = await widget.api.restocks();
      if (!mounted) return;
      setState(() {
        _products = inventory.products;
        _lowStockThreshold = inventory.lowStockThreshold;
        _restocks = restocks;
        _loading = false;
      });
    } on ApiException catch (error) {
      if (error.statusCode == 401 || error.statusCode == 403) {
        await widget.onSignOut();
        return;
      }
      if (mounted) {
        setState(() {
          _loading = false;
          _loadError = error.message;
        });
      }
    } on FormatException catch (error) {
      if (mounted) {
        setState(() {
          _loading = false;
          _loadError = error.message;
        });
      }
    }
  }

  Future<void> _createRestock({
    required int productId,
    required int quantity,
    required DateTime receivedAt,
    String? supplier,
    String? notes,
  }) async {
    await widget.api.createRestock(
      productId: productId,
      quantity: quantity,
      receivedAt: receivedAt,
      supplier: supplier,
      notes: notes,
    );
    await _loadData();
    if (mounted && _loadError == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Stok berhasil ditambahkan.')),
      );
    }
  }

  Future<void> _signOut() async {
    if (_signingOut) return;
    setState(() => _signingOut = true);
    await widget.onSignOut();
  }

  String get _title => switch (_tab) {
    0 => 'Stok produk',
    1 => 'Catat restock',
    _ => 'Riwayat restock',
  };

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          _title,
          style: const TextStyle(fontWeight: FontWeight.w800),
        ),
        actions: [
          PopupMenuButton<String>(
            tooltip: 'Akun',
            onSelected: (value) {
              if (value == 'logout') _signOut();
            },
            itemBuilder: (context) => [
              PopupMenuItem(
                enabled: false,
                child: Text(
                  widget.user.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              const PopupMenuItem(
                value: 'logout',
                child: ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: Icon(Icons.logout),
                  title: Text('Keluar'),
                ),
              ),
            ],
          ),
        ],
      ),
      body: SafeArea(
        child: _loading
            ? const Center(child: CircularProgressIndicator())
            : _loadError != null
            ? _RetryPanel(message: _loadError!, onRetry: _loadData)
            : RefreshIndicator(
                onRefresh: _loadData,
                child: IndexedStack(
                  index: _tab,
                  children: [
                    _InventoryTab(
                      products: _products,
                      lowStockThreshold: _lowStockThreshold,
                    ),
                    _RestockTab(products: _products, onSubmit: _createRestock),
                    _HistoryTab(restocks: _restocks),
                  ],
                ),
              ),
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _tab,
        onDestinationSelected: (value) => setState(() => _tab = value),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.inventory_2_outlined),
            selectedIcon: Icon(Icons.inventory_2),
            label: 'Stok',
          ),
          NavigationDestination(
            icon: Icon(Icons.add_box_outlined),
            selectedIcon: Icon(Icons.add_box),
            label: 'Restock',
          ),
          NavigationDestination(icon: Icon(Icons.history), label: 'Riwayat'),
        ],
      ),
    );
  }
}

class _InventoryTab extends StatelessWidget {
  const _InventoryTab({
    required this.products,
    required this.lowStockThreshold,
  });

  final List<InventoryProduct> products;
  final int lowStockThreshold;

  @override
  Widget build(BuildContext context) {
    final lowCount = products
        .where((product) => product.stock <= lowStockThreshold)
        .length;
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        _SummaryCard(
          title: 'Produk terdaftar',
          value: '${products.length}',
          subtitle:
              '$lowCount produk stoknya $lowStockThreshold unit atau kurang',
        ),
        const SizedBox(height: 14),
        if (products.isEmpty)
          const _EmptyPanel(
            icon: Icons.inventory_2_outlined,
            title: 'Belum ada produk',
            message: 'Tambahkan produk melalui dashboard web terlebih dahulu.',
          )
        else
          ...products.map(
            (product) => Card(
              margin: const EdgeInsets.only(bottom: 10),
              elevation: 0,
              color: Colors.white,
              child: ListTile(
                contentPadding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 6,
                ),
                leading: CircleAvatar(
                  backgroundColor: product.stock <= lowStockThreshold
                      ? const Color(0xFFFFF1D6)
                      : const Color(0xFFECFDF5),
                  foregroundColor: product.stock <= lowStockThreshold
                      ? const Color(0xFF92400E)
                      : const Color(0xFF047857),
                  child: Icon(
                    product.stock <= lowStockThreshold
                        ? Icons.warning_amber_rounded
                        : Icons.check_rounded,
                  ),
                ),
                title: Text(
                  product.name,
                  style: const TextStyle(fontWeight: FontWeight.w700),
                ),
                subtitle: Text(
                  [
                    if (product.category?.isNotEmpty == true) product.category!,
                    product.isActive ? 'Aktif' : 'Nonaktif',
                  ].join(' · '),
                ),
                trailing: Text(
                  '${product.stock}',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    color: product.stock <= lowStockThreshold
                        ? const Color(0xFFB45309)
                        : const Color(0xFF0F172A),
                  ),
                ),
              ),
            ),
          ),
      ],
    );
  }
}

class _RestockTab extends StatefulWidget {
  const _RestockTab({required this.products, required this.onSubmit});

  final List<InventoryProduct> products;
  final Future<void> Function({
    required int productId,
    required int quantity,
    required DateTime receivedAt,
    String? supplier,
    String? notes,
  })
  onSubmit;

  @override
  State<_RestockTab> createState() => _RestockTabState();
}

class _RestockTabState extends State<_RestockTab> {
  final _formKey = GlobalKey<FormState>();
  final _quantity = TextEditingController();
  final _supplier = TextEditingController();
  final _notes = TextEditingController();
  int? _productId;
  DateTime _receivedAt = DateTime.now();
  bool _busy = false;
  String? _error;

  @override
  void dispose() {
    _quantity.dispose();
    _supplier.dispose();
    _notes.dispose();
    super.dispose();
  }

  Future<void> _chooseDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _receivedAt,
      firstDate: DateTime(2000),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (date != null) setState(() => _receivedAt = date);
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate() || _productId == null) return;
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      await widget.onSubmit(
        productId: _productId!,
        quantity: int.parse(_quantity.text),
        receivedAt: _receivedAt,
        supplier: _supplier.text.trim().isEmpty ? null : _supplier.text.trim(),
        notes: _notes.text.trim().isEmpty ? null : _notes.text.trim(),
      );
      if (mounted) {
        _quantity.clear();
        _supplier.clear();
        _notes.clear();
      }
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } on FormatException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (widget.products.isEmpty) {
      return const _EmptyPanel(
        icon: Icons.add_box_outlined,
        title: 'Belum ada produk untuk direstock',
        message: 'Tambahkan produk melalui dashboard web terlebih dahulu.',
      );
    }
    final selectedProduct = widget.products
        .where((product) => product.id == _productId)
        .firstOrNull;

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Card(
          elevation: 0,
          color: Colors.white,
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Text(
                    'Tambah barang masuk',
                    style: Theme.of(context).textTheme.titleLarge
                        ?.copyWith(fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Stok akan langsung diperbarui setelah disimpan.',
                    style: TextStyle(color: Colors.blueGrey),
                  ),
                  const SizedBox(height: 20),
                  DropdownButtonFormField<int>(
                    initialValue: _productId,
                    isExpanded: true,
                    decoration: const InputDecoration(
                      labelText: 'Produk',
                      prefixIcon: Icon(Icons.inventory_2_outlined),
                    ),
                    items: widget.products
                        .map(
                          (product) => DropdownMenuItem(
                            value: product.id,
                            child: Text(
                              '${product.name} · stok ${product.stock}',
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        )
                        .toList(),
                    onChanged: (value) => setState(() => _productId = value),
                    validator: (value) =>
                        value == null ? 'Pilih produk terlebih dahulu.' : null,
                  ),
                  if (selectedProduct != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      'Stok saat ini: ${selectedProduct.stock} unit',
                      style: const TextStyle(
                        color: Color(0xFF92400E),
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                  const SizedBox(height: 14),
                  TextFormField(
                    controller: _quantity,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(
                      labelText: 'Jumlah masuk',
                      prefixIcon: Icon(Icons.add_shopping_cart_outlined),
                      suffixText: 'unit',
                    ),
                    validator: (value) {
                      final quantity = int.tryParse(value ?? '');
                      if (quantity == null || quantity < 1) {
                        return 'Masukkan jumlah minimal 1 unit.';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 14),
                  OutlinedButton.icon(
                    onPressed: _chooseDate,
                    icon: const Icon(Icons.calendar_today_outlined),
                    label: Text(
                      'Tanggal diterima: ${_formatDate(_receivedAt)}',
                    ),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 15),
                      alignment: Alignment.centerLeft,
                    ),
                  ),
                  const SizedBox(height: 14),
                  TextFormField(
                    controller: _supplier,
                    textCapitalization: TextCapitalization.words,
                    maxLength: 255,
                    decoration: const InputDecoration(
                      labelText: 'Pemasok (opsional)',
                      prefixIcon: Icon(Icons.local_shipping_outlined),
                    ),
                  ),
                  TextFormField(
                    controller: _notes,
                    minLines: 2,
                    maxLines: 4,
                    maxLength: 2000,
                    decoration: const InputDecoration(
                      labelText: 'Catatan (opsional)',
                      alignLabelWithHint: true,
                      prefixIcon: Icon(Icons.notes_outlined),
                    ),
                  ),
                  if (_error != null) ...[
                    const SizedBox(height: 12),
                    _MessageBanner(message: _error!),
                  ],
                  const SizedBox(height: 18),
                  FilledButton.icon(
                    onPressed: _busy ? null : _submit,
                    icon: _busy
                        ? const SizedBox.square(
                            dimension: 18,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                        : const Icon(Icons.save_outlined),
                    label: Text(_busy ? 'Menyimpan...' : 'Simpan restock'),
                    style: FilledButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _HistoryTab extends StatelessWidget {
  const _HistoryTab({required this.restocks});

  final List<RestockRecord> restocks;

  @override
  Widget build(BuildContext context) {
    if (restocks.isEmpty) {
      return const _EmptyPanel(
        icon: Icons.history,
        title: 'Belum ada riwayat',
        message: 'Transaksi barang masuk akan tampil di sini.',
      );
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: restocks.length,
      separatorBuilder: (context, _) => const SizedBox(height: 10),
      itemBuilder: (context, index) {
        final record = restocks[index];
        return Card(
          elevation: 0,
          color: Colors.white,
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const CircleAvatar(
                      backgroundColor: Color(0xFFECFDF5),
                      foregroundColor: Color(0xFF047857),
                      child: Icon(Icons.south_west_rounded),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            record.productName,
                            style: const TextStyle(fontWeight: FontWeight.w800),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            _formatDateTime(record.receivedAt),
                            style: const TextStyle(
                              color: Colors.blueGrey,
                              fontSize: 12,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Text(
                      '+${record.quantity}',
                      style: const TextStyle(
                        color: Color(0xFF047857),
                        fontSize: 18,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                  ],
                ),
                if (record.supplier?.isNotEmpty == true ||
                    record.userName?.isNotEmpty == true) ...[
                  const Divider(height: 24),
                  Wrap(
                    spacing: 16,
                    runSpacing: 8,
                    children: [
                      if (record.supplier?.isNotEmpty == true)
                        _MetaLabel(label: 'Pemasok', value: record.supplier!),
                      if (record.userName?.isNotEmpty == true)
                        _MetaLabel(
                          label: 'Dicatat oleh',
                          value: record.userName!,
                        ),
                    ],
                  ),
                ],
                if (record.notes?.isNotEmpty == true) ...[
                  const SizedBox(height: 12),
                  Text(
                    record.notes!,
                    style: const TextStyle(color: Colors.blueGrey),
                  ),
                ],
              ],
            ),
          ),
        );
      },
    );
  }
}

class _SummaryCard extends StatelessWidget {
  const _SummaryCard({
    required this.title,
    required this.value,
    required this.subtitle,
  });

  final String title;
  final String value;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      color: const Color(0xFF0F172A),
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Row(
          children: [
            const Icon(
              Icons.inventory_2_outlined,
              color: Color(0xFFFCD34D),
              size: 28,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(color: Color(0xFFCBD5E1))),
                  Text(
                    value,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  Text(
                    subtitle,
                    style: const TextStyle(
                      color: Color(0xFFCBD5E1),
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _MessageBanner extends StatelessWidget {
  const _MessageBanner({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(12),
    decoration: BoxDecoration(
      color: const Color(0xFFFFF1F2),
      borderRadius: BorderRadius.circular(12),
    ),
    child: Text(message, style: const TextStyle(color: Color(0xFF9F1239))),
  );
}

class _RetryPanel extends StatelessWidget {
  const _RetryPanel({required this.message, required this.onRetry});

  final String message;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) => Center(
    child: Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.cloud_off_outlined, size: 40, color: Colors.grey),
          const SizedBox(height: 12),
          Text(message, textAlign: TextAlign.center),
          const SizedBox(height: 16),
          FilledButton.icon(
            onPressed: onRetry,
            icon: const Icon(Icons.refresh),
            label: const Text('Coba lagi'),
          ),
        ],
      ),
    ),
  );
}

class _EmptyPanel extends StatelessWidget {
  const _EmptyPanel({
    required this.icon,
    required this.title,
    required this.message,
  });

  final IconData icon;
  final String title;
  final String message;

  @override
  Widget build(BuildContext context) => Center(
    child: Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 42, color: const Color(0xFF94A3B8)),
          const SizedBox(height: 12),
          Text(
            title,
            textAlign: TextAlign.center,
            style: const TextStyle(fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 6),
          Text(
            message,
            textAlign: TextAlign.center,
            style: const TextStyle(color: Colors.blueGrey),
          ),
        ],
      ),
    ),
  );
}

class _MetaLabel extends StatelessWidget {
  const _MetaLabel({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) => Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Text(label, style: const TextStyle(color: Colors.blueGrey, fontSize: 11)),
      const SizedBox(height: 2),
      Text(
        value,
        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
      ),
    ],
  );
}

String _formatDate(DateTime date) =>
    '${date.day.toString().padLeft(2, '0')}/${date.month.toString().padLeft(2, '0')}/${date.year}';

String _formatDateTime(DateTime date) =>
    '${_formatDate(date)} · ${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
