import 'dart:async';
import 'dart:convert';

import 'package:http/http.dart' as http;

const apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'http://10.0.2.2:8000/api/mobile',
);

class ApiException implements Exception {
  const ApiException(this.message, {this.statusCode});

  final String message;
  final int? statusCode;

  @override
  String toString() => message;
}

class MobileUser {
  const MobileUser({required this.id, required this.name, required this.email});

  final int id;
  final String name;
  final String email;

  factory MobileUser.fromJson(Map<String, dynamic> json) => MobileUser(
    id: _requiredInt(json, 'id'),
    name: _requiredString(json, 'name'),
    email: _requiredString(json, 'email'),
  );

  Map<String, dynamic> toJson() => {'id': id, 'name': name, 'email': email};
}

class LoginResult {
  const LoginResult({required this.token, required this.user});

  final String token;
  final MobileUser user;
}

class InventoryProduct {
  const InventoryProduct({
    required this.id,
    required this.name,
    required this.stock,
    required this.isActive,
    this.category,
  });

  final int id;
  final String name;
  final int stock;
  final bool isActive;
  final String? category;

  factory InventoryProduct.fromJson(Map<String, dynamic> json) =>
      InventoryProduct(
        id: _requiredInt(json, 'id'),
        name: _requiredString(json, 'name'),
        stock: _requiredInt(json, 'stock'),
        isActive: json['is_active'] == true,
        category: json['category'] as String?,
      );
}

class ProductInventory {
  const ProductInventory({
    required this.products,
    required this.lowStockThreshold,
  });

  final List<InventoryProduct> products;
  final int lowStockThreshold;
}

class RestockRecord {
  const RestockRecord({
    required this.id,
    required this.productName,
    required this.quantity,
    required this.receivedAt,
    this.supplier,
    this.notes,
    this.userName,
  });

  final int id;
  final String productName;
  final int quantity;
  final DateTime receivedAt;
  final String? supplier;
  final String? notes;
  final String? userName;

  factory RestockRecord.fromJson(Map<String, dynamic> json) {
    final receivedAt = DateTime.tryParse(_requiredString(json, 'received_at'));
    if (receivedAt == null) {
      throw const FormatException('Tanggal restock dari server tidak valid.');
    }

    final user = json['user'];
    return RestockRecord(
      id: _requiredInt(json, 'id'),
      productName: _requiredString(json, 'product_name'),
      quantity: _requiredInt(json, 'quantity'),
      receivedAt: receivedAt,
      supplier: json['supplier'] as String?,
      notes: json['notes'] as String?,
      userName: user is Map<String, dynamic> ? user['name'] as String? : null,
    );
  }
}

class ApiClient {
  ApiClient({String? accessToken, String? baseUrl})
    : _token = accessToken,
      _baseUrl = (baseUrl ?? apiBaseUrl).replaceFirst(RegExp(r'/+$'), '');

  final String? _token;
  final String _baseUrl;
  final http.Client _http = http.Client();

  Future<LoginResult> login(String email, String password) async {
    final result = await _post('/login', {
      'email': email,
      'password': password,
    });
    final token = result['token'];
    if (token is! String || token.isEmpty) {
      throw const FormatException(
        'Token login tidak ditemukan pada respons server.',
      );
    }
    return LoginResult(
      token: token,
      user: MobileUser.fromJson(_asMap(result['user'])),
    );
  }

  Future<MobileUser> me() async => MobileUser.fromJson(await _get('/me'));

  Future<void> logout() async {
    await _post('/logout', const {});
  }

  Future<ProductInventory> products() async {
    final result = await _get('/products');
    final data = result['data'];
    final threshold = result['low_stock_threshold'];
    if (data is! List || threshold is! int) {
      throw const FormatException('Daftar produk dari server tidak valid.');
    }
    return ProductInventory(
      products: data
          .map((item) => InventoryProduct.fromJson(_asMap(item)))
          .toList(growable: false),
      lowStockThreshold: threshold,
    );
  }

  Future<List<RestockRecord>> restocks() async {
    final result = await _get('/restocks');
    final data = result['data'];
    if (data is! List) {
      throw const FormatException('Riwayat restock dari server tidak valid.');
    }
    return data
        .map((item) => RestockRecord.fromJson(_asMap(item)))
        .toList(growable: false);
  }

  Future<void> createRestock({
    required int productId,
    required int quantity,
    required DateTime receivedAt,
    String? supplier,
    String? notes,
  }) async {
    await _post('/restocks', {
      'product_id': productId,
      'quantity': quantity,
      'received_at':
          '${receivedAt.year.toString().padLeft(4, '0')}-${receivedAt.month.toString().padLeft(2, '0')}-${receivedAt.day.toString().padLeft(2, '0')}',
      'supplier': supplier,
      'notes': notes,
    });
  }

  void close() => _http.close();

  Future<Map<String, dynamic>> _get(String path) =>
      _send(() => _http.get(_uri(path), headers: _headers));

  Future<Map<String, dynamic>> _post(String path, Map<String, Object?> body) =>
      _send(
        () => _http.post(_uri(path), headers: _headers, body: jsonEncode(body)),
      );

  Uri _uri(String path) => Uri.parse('$_baseUrl$path');

  Map<String, String> get _headers => {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  Future<Map<String, dynamic>> _send(
    Future<http.Response> Function() request,
  ) async {
    late final http.Response response;
    try {
      response = await request().timeout(const Duration(seconds: 20));
    } on http.ClientException {
      throw const ApiException('Tidak dapat terhubung ke server.');
    } on TimeoutException {
      throw const ApiException('Server tidak merespons. Coba lagi sebentar.');
    }

    final decoded = response.body.isEmpty
        ? <String, dynamic>{}
        : _asMap(jsonDecode(response.body));
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw ApiException(
        _errorMessage(decoded, response.statusCode),
        statusCode: response.statusCode,
      );
    }
    return decoded;
  }
}

Map<String, dynamic> _asMap(Object? value) {
  if (value is Map<String, dynamic>) return value;
  throw const FormatException('Respons server tidak berbentuk objek JSON.');
}

int _requiredInt(Map<String, dynamic> json, String key) {
  final value = json[key];
  if (value is int) return value;
  if (value is num) return value.toInt();
  throw FormatException('Data $key dari server tidak valid.');
}

String _requiredString(Map<String, dynamic> json, String key) {
  final value = json[key];
  if (value is String) return value;
  throw FormatException('Data $key dari server tidak valid.');
}

String _errorMessage(Map<String, dynamic> body, int statusCode) {
  final errors = body['errors'];
  if (errors is Map<String, dynamic>) {
    for (final messages in errors.values) {
      if (messages is List && messages.isNotEmpty && messages.first is String) {
        return messages.first as String;
      }
    }
  }
  final message = body['message'];
  if (message is String && message.isNotEmpty) return message;
  return 'Permintaan gagal (HTTP $statusCode).';
}
