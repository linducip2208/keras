import 'package:dio/dio.dart';
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
                        onTap: () => _showDetail(s['id']),
                      ),
                    );
                  },
                ),
          ),
    );
  }

  Future<void> _showDetail(dynamic id) async {
    showModalBottomSheet(
      context: context,
      builder: (_) => const Center(child: Padding(
        padding: EdgeInsets.all(24),
        child: CircularProgressIndicator(),
      )),
    );
    try {
      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.get('/payslips/$id');
      if (!mounted) return;
      Navigator.of(context).pop();
      final d = (resp.data['data'] as Map<String, dynamic>);
      if (!mounted) return;
      showModalBottomSheet(
        // ignore: use_build_context_synchronously
        context: context,
        isScrollControlled: true,
        builder: (_) => _PayslipDetailSheet(data: d),
      );
    } on DioException catch (e) {
      if (!mounted) return;
      Navigator.of(context).pop();
      final msg = e.response?.data?['error']?.toString() ?? 'Gagal memuat detail slip.';
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
    }
  }
}

class _PayslipDetailSheet extends StatelessWidget {
  final Map<String, dynamic> data;
  const _PayslipDetailSheet({required this.data});

  @override
  Widget build(BuildContext context) {
    final item = (data['payroll_item'] ?? {}) as Map<String, dynamic>;
    final comps = ((item['components'] ?? []) as List)
        .map((e) => (e as Map<String, dynamic>))
        .toList();
    final earnings = comps.where((c) => (c['component']?['type'] ?? '') == 'earning').toList();
    final deductions = comps.where((c) => (c['component']?['type'] ?? '') != 'earning').toList();

    Widget row(String label, dynamic amount, {bool bold = false}) {
      return Padding(
        padding: const EdgeInsets.symmetric(vertical: 4),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label, style: TextStyle(fontWeight: bold ? FontWeight.bold : FontWeight.normal)),
            Text('Rp ${_fmt(amount)}',
                style: TextStyle(fontWeight: bold ? FontWeight.bold : FontWeight.normal)),
          ],
        ),
      );
    }

    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('Slip ${data['slip_number'] ?? ''}',
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            Text('Periode: ${data['slip_date'] ?? '-'}',
                style: const TextStyle(color: Colors.black54)),
            const SizedBox(height: 16),
            const Text('Pendapatan', style: TextStyle(fontWeight: FontWeight.bold)),
            ...earnings.map((c) => row(
              (c['component']?['name'] ?? '-').toString(), c['amount'])),
            const Divider(),
            const Text('Potongan', style: TextStyle(fontWeight: FontWeight.bold)),
            ...deductions.map((c) => row(
              (c['component']?['name'] ?? '-').toString(), c['amount'])),
            row('PPh 21', item['pph_21']),
            row('BPJS Karyawan', item['bpjs_employee']),
            const Divider(),
            row('Gaji Bersih', item['net_pay'], bold: true),
          ],
        ),
      ),
    );
  }

  String _fmt(dynamic v) {
    final n = v is num ? v : 0;
    final s = n.toStringAsFixed(0);
    return s.replaceAllMapped(RegExp(r'\B(?=(\d{3})+(?!\d))'), (_) => '.');
  }
}
