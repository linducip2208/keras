import 'package:dio/dio.dart';
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
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  String _typeName(Map<String, dynamic> m) {
    final rel = m['leaveType'] ?? m['leave_type'];
    if (rel is Map) return (rel['name'] ?? 'Cuti').toString();
    return 'Cuti';
  }

  double _num(dynamic v) => (v is num) ? v.toDouble() : 0;

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final dio = ref.read(apiClientProvider).dio;
      final bal = await dio.get('/leave/balances');
      final req = await dio.get('/leave/requests');
      if (!mounted) return;
      setState(() {
        _balances = (bal.data['data'] as List?) ?? [];
        _requests = (req.data['data'] as List?) ?? [];
      });
    } on DioException catch (e) {
      if (!mounted) return;
      setState(() => _error = e.response?.data?['error']?.toString() ?? 'Gagal memuat data cuti.');
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
        : _error != null
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(mainAxisSize: MainAxisSize.min, children: [
                  Text(_error!, textAlign: TextAlign.center),
                  const SizedBox(height: 12),
                  ElevatedButton(onPressed: _load, child: const Text('Coba Lagi')),
                ]),
              ),
            )
          : RefreshIndicator(
              onRefresh: _load,
              child: _balances.isEmpty && _requests.isEmpty
                ? ListView(children: const [
                    SizedBox(height: 120),
                    Center(child: Text('Belum ada data cuti.')),
                  ])
                : ListView(
                    padding: const EdgeInsets.all(20),
                    children: [
                      const Text('Saldo Cuti', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 12),
                      ..._balances.map((b) {
                        final m = (b as Map<String, dynamic>);
                        final sisa = _num(m['allocated']) - _num(m['used']) - _num(m['pending']);
                        return Card(
                          child: ListTile(
                            title: Text(_typeName(m)),
                            subtitle: Text('Terpakai: ${_num(m['used']).toStringAsFixed(1)} hari'),
                            trailing: Text(
                              '${sisa.toStringAsFixed(1)} / ${_num(m['allocated']).toStringAsFixed(0)}',
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                            ),
                          ),
                        );
                      }),
                      const SizedBox(height: 20),
                      const Text('Riwayat Pengajuan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 12),
                      ..._requests.map((r) {
                        final m = (r as Map<String, dynamic>);
                        final status = (m['status'] ?? '-').toString();
                        return Card(
                          child: ListTile(
                            title: Text(_typeName(m)),
                            subtitle: Text('${m['start_date']} → ${m['end_date']} (${m['days_count']} hari)'),
                            trailing: Chip(
                              label: Text(status, style: const TextStyle(fontSize: 11)),
                              backgroundColor: _statusColor(status).withValues(alpha: 0.15),
                              labelStyle: TextStyle(color: _statusColor(status)),
                            ),
                          ),
                        );
                      }),
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

  Future<void> _showRequestSheet() async {
    final submitted = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (_) => const _LeaveRequestSheet(),
    );
    if (submitted == true) _load();
  }
}

class _LeaveRequestSheet extends ConsumerStatefulWidget {
  const _LeaveRequestSheet();

  @override
  ConsumerState<_LeaveRequestSheet> createState() => _LeaveRequestSheetState();
}

class _LeaveRequestSheetState extends ConsumerState<_LeaveRequestSheet> {
  List<dynamic> _types = [];
  int? _typeId;
  DateTime? _start;
  DateTime? _end;
  bool _halfDay = false;
  final _reasonCtrl = TextEditingController();
  bool _loading = true;
  bool _submitting = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadTypes();
  }

  Future<void> _loadTypes() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.get('/leave/types');
      if (!mounted) return;
      final list = (resp.data['data'] as List?) ?? [];
      setState(() {
        _types = list;
        _loading = false;
        if (list.isNotEmpty) _typeId = (list.first as Map)['id'] as int?;
      });
    } on DioException catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = e.response?.data?['error']?.toString() ?? 'Gagal memuat jenis cuti.';
      });
    }
  }

  Future<void> _pickDate(bool isStart) async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: isStart ? (_start ?? now) : (_end ?? _start ?? now),
      firstDate: now.subtract(const Duration(days: 30)),
      lastDate: now.add(const Duration(days: 365)),
    );
    if (picked == null) return;
    setState(() {
      if (isStart) {
        _start = picked;
        if (_end != null && _end!.isBefore(picked)) _end = picked;
      } else {
        _end = picked;
      }
    });
  }

  Future<void> _submit() async {
    if (_typeId == null || _start == null || _end == null) {
      setState(() => _error = 'Lengkapi jenis dan tanggal cuti.');
      return;
    }
    setState(() { _submitting = true; _error = null; });
    try {
      final dio = ref.read(apiClientProvider).dio;
      await dio.post('/leave/requests', data: {
        'leave_type_id': _typeId,
        'start_date': _fmt(_start!),
        'end_date': _fmt(_end!),
        'reason': _reasonCtrl.text,
        'is_half_day': _halfDay,
      });
      if (!mounted) return;
      Navigator.of(context).pop(true);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pengajuan cuti terkirim.')),
      );
    } on DioException catch (e) {
      if (!mounted) return;
      final msg = e.response?.data?['error']?.toString()
          ?? e.response?.data?['message']?.toString()
          ?? 'Gagal mengirim pengajuan.';
      setState(() => _error = msg);
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  String _fmt(DateTime d) => '${d.year}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Padding(
        padding: EdgeInsets.only(
          left: 20, right: 20, top: 20,
          bottom: MediaQuery.of(context).viewInsets.bottom + 20,
        ),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text('Pengajuan Cuti', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 16),
              if (_loading)
                const Center(child: CircularProgressIndicator())
              else ...[
                DropdownButtonFormField<int>(
                  initialValue: _typeId,
                  decoration: const InputDecoration(labelText: 'Jenis Cuti'),
                  items: _types.map((t) {
                    final m = (t as Map<String, dynamic>);
                    return DropdownMenuItem<int>(
                      value: m['id'] as int,
                      child: Text((m['name'] ?? '-').toString()),
                    );
                  }).toList(),
                  onChanged: (v) => setState(() => _typeId = v),
                ),
                const SizedBox(height: 12),
                Row(children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => _pickDate(true),
                      child: Text(_start == null ? 'Mulai' : _fmt(_start!)),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => _pickDate(false),
                      child: Text(_end == null ? 'Selesai' : _fmt(_end!)),
                    ),
                  ),
                ]),
                SwitchListTile(
                  title: const Text('Setengah hari'),
                  value: _halfDay,
                  onChanged: (v) => setState(() => _halfDay = v),
                ),
                TextField(
                  controller: _reasonCtrl,
                  decoration: const InputDecoration(labelText: 'Alasan'),
                  maxLines: 2,
                ),
              ],
              if (_error != null) ...[
                const SizedBox(height: 12),
                Text(_error!, style: const TextStyle(color: Colors.red)),
              ],
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: (_submitting || _loading) ? null : _submit,
                child: _submitting
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                  : const Text('Kirim Pengajuan'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
