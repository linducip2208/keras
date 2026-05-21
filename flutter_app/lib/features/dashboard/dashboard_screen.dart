import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/auth/auth_provider.dart';
import '../attendance/attendance_screen.dart';
import '../leave/leave_screen.dart';
import '../payslip/payslip_screen.dart';
import '../approval/approval_screen.dart';

class DashboardScreen extends ConsumerStatefulWidget {
  const DashboardScreen({super.key});

  @override
  ConsumerState<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends ConsumerState<DashboardScreen> {
  int _index = 0;

  final _screens = const [
    HomeTab(),
    AttendanceScreen(),
    LeaveScreen(),
    PayslipScreen(),
    ApprovalScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _screens[_index],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.fingerprint), selectedIcon: Icon(Icons.fingerprint), label: 'Absen'),
          NavigationDestination(icon: Icon(Icons.event_note_outlined), selectedIcon: Icon(Icons.event_note), label: 'Cuti'),
          NavigationDestination(icon: Icon(Icons.receipt_long_outlined), selectedIcon: Icon(Icons.receipt_long), label: 'Slip'),
          NavigationDestination(icon: Icon(Icons.approval_outlined), selectedIcon: Icon(Icons.approval), label: 'Approval'),
        ],
      ),
    );
  }
}

class HomeTab extends ConsumerWidget {
  const HomeTab({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authStateProvider).valueOrNull;
    final employee = auth?.employee;
    final name = (employee?['full_name'] as String?) ?? (auth?.user?['name'] as String?) ?? 'Karyawan';

    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: Theme.of(context).colorScheme.primary,
                  child: Text(name.substring(0, 1).toUpperCase(), style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Halo,', style: TextStyle(color: Colors.black54)),
                      Text(name, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.logout),
                  onPressed: () async {
                    await ref.read(authServiceProvider).logout();
                    ref.invalidate(authStateProvider);
                  },
                ),
              ],
            ),
            const SizedBox(height: 24),

            // Quick clock-in card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  children: [
                    const Icon(Icons.fingerprint, size: 64, color: Color(0xFFF97316)),
                    const SizedBox(height: 8),
                    const Text('Belum Clock-in Hari Ini', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600)),
                    const SizedBox(height: 16),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        icon: const Icon(Icons.touch_app),
                        label: const Text('Clock-in Sekarang'),
                        onPressed: () {
                          // Navigate to attendance tab
                        },
                      ),
                    ),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 20),
            const Text('Menu Cepat', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 12),
            GridView.count(
              crossAxisCount: 4,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
              children: [
                _MenuItem(icon: Icons.event_note, label: 'Cuti', color: Colors.blue, onTap: () {}),
                _MenuItem(icon: Icons.access_time, label: 'Lembur', color: Colors.orange, onTap: () {}),
                _MenuItem(icon: Icons.receipt, label: 'Reimburse', color: Colors.green, onTap: () {}),
                _MenuItem(icon: Icons.receipt_long, label: 'Slip Gaji', color: Colors.purple, onTap: () {}),
                _MenuItem(icon: Icons.people, label: 'Tim', color: Colors.teal, onTap: () {}),
                _MenuItem(icon: Icons.calendar_today, label: 'Kalender', color: Colors.red, onTap: () {}),
                _MenuItem(icon: Icons.school, label: 'Training', color: Colors.indigo, onTap: () {}),
                _MenuItem(icon: Icons.account_circle, label: 'Profil', color: Colors.brown, onTap: () {}),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _MenuItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _MenuItem({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 6),
          Text(label, style: const TextStyle(fontSize: 12)),
        ],
      ),
    );
  }
}
