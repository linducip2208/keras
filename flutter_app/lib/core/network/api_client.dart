import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  static const String defaultBaseUrl = 'http://10.0.2.2:8000/api/v1'; // Android emulator → host
  static const _storage = FlutterSecureStorage();
  static const _tokenKey = 'auth_token';

  /// Dipasang dari AuthGate: dipanggil saat API mengembalikan 401,
  /// agar UI dipaksa kembali ke Login (bukan menggantung di Dashboard).
  static void Function()? onUnauthorized;

  late final Dio dio;

  ApiClient({String? baseUrl}) {
    dio = Dio(BaseOptions(
      baseUrl: baseUrl ?? defaultBaseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 60),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: _tokenKey);
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (e, handler) {
        // Logout on 401 + paksa UI kembali ke Login via hook.
        if (e.response?.statusCode == 401) {
          _storage.delete(key: _tokenKey);
          try {
            onUnauthorized?.call();
          } catch (_) {}
        }
        handler.next(e);
      },
    ));
  }

  static Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  static Future<String?> getToken() => _storage.read(key: _tokenKey);

  static Future<void> clearToken() => _storage.delete(key: _tokenKey);
}
