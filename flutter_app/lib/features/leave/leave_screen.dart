import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/auth/auth_provider.dart';

class LeaveScreen extends ConsumerStatefulWidget {
  const LeaveScreen({super.key});

  @override
  ConsumerState<LeaveScreen> createState() => _LeaveScreenState();
}

class _LeaveScreenState extends ConsumerState<LeaveScreen> {
  List<dynamic> _balances = [];
  List<dynamic> _requests = [];
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
      final bal = await dio.get('/leave/balances');
      final req = await dio.get('/leave/requests');
      setState(() {
        _balances = bal.data['data'];
        _requests = req.data['data'];
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Cuti'),
        actions: [
          IconButton(icon: const Icon(Icons.add), onPressed: () => _showRequestSheet()),
        ],
      ),
      body: _loading
        ? const Center(child: CircularProgressIndicator())
        : RefreshIndicator(
            onRefresh: _load,
            child: ListView(
              padding: const EdgeInsets.all(20),
              children: [
                const Text('Saldo Cuti', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const SizedBox(height: 12),
                ..._balances.map((b) => Card(
                  child: ListTile(
                    title: Text((b['leave_type']?['name']) ?? 'Cuti'),
                    subtitle: Text('Terpakai: ${b['used']} hari'),
                    trailing: Text(
                      '${(b['allocated'] as num) - (b['used'] as num) - (b['pending'] as num)} / ${b['allocated']}',
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                  ),
                )),
                const SizedBox(height: 20),
                const Text('Riwayat Pengajuan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const SizedBox(height: 12),
                ..._requests.map((r) => Card(
                  child: ListTile(
                    title: Text((r['leave_type']?['name']) ?? 'Cuti'),
                    subtitle: Text('${r['start_date']} → ${r['end_date']} (${r['days_count']} hari)'),
                    trailing: Chip(
                      label: Text(r['status'], style: const TextStyle(fontSize: 11)),
                      backgroundColor: _statusColor(r['status']).withValues(alpha: 0.15),
                      labelStyle: TextStyle(color: _statusColor(r['status'])),
                    ),
                  ),
                )),
              ],
            ),
          ),
    );
  }

  Color _statusColor(String s) => switch (s) {
    'approved' => Colors.green,
    'rejected' => Colors.red,
    'pending' => Colors.orange,
    _ => Colors.grey,
  };

  void _showRequestSheet() {
    // TODO: implement leave request form sheet (pick type, dates, reason)
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Form request cuti akan ditambahkan.')));
  }
}
