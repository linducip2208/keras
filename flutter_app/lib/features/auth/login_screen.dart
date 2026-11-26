import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/auth/auth_provider.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  // Prefill demo HANYA untuk debug build — tidak ikut ke release.
  final _emailCtrl = TextEditingController(text: kDebugMode ? 'employee@hris.test' : '');
  final _passwordCtrl = TextEditingController(text: kDebugMode ? 'password' : '');
  bool _loading = false;
  bool _obscure = true;
  String? _error;

  Future<void> _login() async {
    setState(() { _loading = true; _error = null; });
    try {
      await ref.read(authServiceProvider).login(
        _emailCtrl.text.trim(),
        _passwordCtrl.text,
        'mobile-app',
      );
      // ignore: use_build_context_synchronously
      ref.invalidate(authStateProvider);
    } on DioException catch (e) {
      final msg = e.response?.data?['message'] ?? e.response?.data?['error'] ?? 'Periksa koneksi internet.';
      setState(() => _error = 'Login gagal: $msg');
    } catch (e) {
      setState(() => _error = 'Login gagal: $e');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 60),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    color: Theme.of(context).colorScheme.primary,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  alignment: Alignment.center,
                  child: const Text('H', style: TextStyle(fontSize: 48, color: Colors.white, fontWeight: FontWeight.w800)),
                ),
              ),
              const SizedBox(height: 32),
              Text('Selamat Datang', textAlign: TextAlign.center, style: Theme.of(context).textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w800)),
              const SizedBox(height: 8),
              const Text('Masuk ke HRIS', textAlign: TextAlign.center, style: TextStyle(color: Colors.black54)),
              const SizedBox(height: 40),
              TextField(controller: _emailCtrl, decoration: const InputDecoration(labelText: 'Email', prefixIcon: Icon(Icons.email_outlined))),
              const SizedBox(height: 16),
              TextField(controller: _passwordCtrl, obscureText: _obscure,
                decoration: InputDecoration(
                  labelText: 'Password',
                  prefixIcon: const Icon(Icons.lock_outline),
                  suffixIcon: IconButton(
                    icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                    onPressed: () => setState(() => _obscure = !_obscure),
                  ),
                )),
              if (_error != null) ...[
                const SizedBox(height: 12),
                Text(_error!, style: const TextStyle(color: Colors.red)),
              ],
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: _loading ? null : _login,
                child: _loading
                  ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Text('Masuk'),
              ),
              const SizedBox(height: 16),
              const Center(child: Text('Lupa password? Hubungi HR Anda.', style: TextStyle(color: Colors.black54))),
            ],
          ),
        ),
      ),
    );
  }
}
