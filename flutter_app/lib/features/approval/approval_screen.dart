import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/auth/auth_provider.dart';

class ApprovalScreen extends ConsumerStatefulWidget {
  const ApprovalScreen({super.key});

  @override
  ConsumerState<ApprovalScreen> createState() => _ApprovalScreenState();
}

class _ApprovalScreenState extends ConsumerState<ApprovalScreen> {
  List<dynamic> _inbox = [];
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
      final resp = await dio.get('/approvals/inbox');
      setState(() => _inbox = resp.data['data']);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _approve(int id) async {
    final dio = ref.read(apiClientProvider).dio;
    await dio.post('/approvals/$id/approve', data: {'comment': 'OK'});
    _load();
  }

  Future<void> _reject(int id) async {
    final dio = ref.read(apiClientProvider).dio;
    await dio.post('/approvals/$id/reject', data: {'comment': 'Rejected via mobile'});
    _load();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Approval Inbox')),
      body: _loading
        ? const Center(child: CircularProgressIndicator())
        : RefreshIndicator(
            onRefresh: _load,
            child: _inbox.isEmpty
              ? const Center(child: Text('Inbox kosong 🎉'))
              : ListView.builder(
                  padding: const EdgeInsets.all(20),
                  itemCount: _inbox.length,
                  itemBuilder: (_, i) {
                    final step = _inbox[i];
                    final req = step['approval_request'];
                    return Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.event_note, color: Theme.of(context).colorScheme.primary),
                                const SizedBox(width: 8),
                                Text('Request #${req['id']}', style: const TextStyle(fontWeight: FontWeight.bold)),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(req['requestable_type']?.toString().split('\\').last ?? 'Request'),
                            const SizedBox(height: 12),
                            Row(
                              children: [
                                Expanded(
                                  child: OutlinedButton.icon(
                                    icon: const Icon(Icons.close, color: Colors.red),
                                    label: const Text('Tolak', style: TextStyle(color: Colors.red)),
                                    onPressed: () => _reject(step['id']),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: ElevatedButton.icon(
                                    icon: const Icon(Icons.check),
                                    label: const Text('Setujui'),
                                    onPressed: () => _approve(step['id']),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
          ),
    );
  }
}
