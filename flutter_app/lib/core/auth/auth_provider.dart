import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../network/api_client.dart';

class AuthState {
  final bool isAuthenticated;
  final Map<String, dynamic>? user;
  final Map<String, dynamic>? employee;
  AuthState({this.isAuthenticated = false, this.user, this.employee});
}

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient());

final authStateProvider = FutureProvider<AuthState>((ref) async {
  final token = await ApiClient.getToken();
  if (token == null) return AuthState(isAuthenticated: false);

  try {
    final dio = ref.read(apiClientProvider).dio;
    final resp = await dio.get('/me');
    return AuthState(
      isAuthenticated: true,
      user: resp.data['user'],
      employee: resp.data['employee'],
    );
  } on DioException {
    await ApiClient.clearToken();
    return AuthState(isAuthenticated: false);
  }
});

class AuthService {
  final Dio dio;
  AuthService(this.dio);

  Future<AuthState> login(String email, String password, String deviceName) async {
    final resp = await dio.post('/auth/login', data: {
      'email': email,
      'password': password,
      'device_name': deviceName,
    });
    await ApiClient.saveToken(resp.data['token']);
    return AuthState(isAuthenticated: true, user: resp.data['user']);
  }

  Future<void> logout() async {
    try {
      await dio.post('/auth/logout');
    } catch (_) {}
    await ApiClient.clearToken();
  }
}

final authServiceProvider = Provider<AuthService>((ref) {
  return AuthService(ref.read(apiClientProvider).dio);
});
