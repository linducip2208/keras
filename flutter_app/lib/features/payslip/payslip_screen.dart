import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/auth/auth_provider.dart';

class PayslipScreen extends ConsumerStatefulWidget {
  const PayslipScreen({super.key});

  @override
  ConsumerState<PayslipScreen> createState() => _PayslipScreenState();
}

class _PayslipScreenState extends ConsumerState<PayslipScreen> {
  List<dynamic> _slips = [];
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.get('/payslips');
      setState(() => _slips = resp.data['data']);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Slip Gaji')),
      body: _loading
        ? const Center(child: CircularProgressIndicator())
        : RefreshIndicator(
            onRefresh: _load,
            child: _slips.isEmpty
              ? const Center(child: Text('Belum ada slip gaji.'))
              : ListView.builder(
                  padding: const EdgeInsets.all(20),
                  itemCount: _slips.length,
                  itemBuilder: (_, i) {
                    final s = _slips[i];
                    return Card(
                      child: ListTile(
                        leading: const Icon(Icons.receipt_long, color: Color(0xFFF97316), size: 32),
                        title: Text('Slip ${s['slip_number']}'),
                        subtitle: Text('Periode: ${s['slip_date']}'),
                        trailing: const Icon(Icons.chevron_right),
                        onTap: () {
                          // TODO: open PDF dengan password (NIK/DOB) locked
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Open password-protected PDF (NIK lock)')),
                          );
                        },
                      ),
                    );
                  },
                ),
          ),
    );
  }
}
