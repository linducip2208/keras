import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';

import '../../core/auth/auth_provider.dart';

class AttendanceScreen extends ConsumerStatefulWidget {
  const AttendanceScreen({super.key});

  @override
  ConsumerState<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends ConsumerState<AttendanceScreen> {
  bool _loading = false;
  String? _message;
  Map<String, dynamic>? _todayLog;

  @override
  void initState() {
    super.initState();
    _loadToday();
  }

  Future<void> _loadToday() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.get('/attendance/today');
      setState(() => _todayLog = resp.data['data']);
    } catch (_) {}
  }

  Future<Position?> _getLocation() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) return null;

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) return null;
    }
    if (permission == LocationPermission.deniedForever) return null;

    return await Geolocator.getCurrentPosition(
      desiredAccuracy: LocationAccuracy.high,
    );
  }

  Future<XFile?> _takeSelfie() async {
    final picker = ImagePicker();
    return await picker.pickImage(
      source: ImageSource.camera,
      preferredCameraDevice: CameraDevice.front,
      imageQuality: 70,
    );
  }

  Future<void> _checkIn() async {
    setState(() { _loading = true; _message = null; });
    try {
      final position = await _getLocation();
      if (position == null) {
        setState(() => _message = 'GPS tidak tersedia atau ditolak');
        return;
      }
      final selfie = await _takeSelfie();
      if (selfie == null) {
        setState(() => _message = 'Selfie dibatalkan');
        return;
      }

      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.post('/attendance/check-in', data: {
        'latitude': position.latitude,
        'longitude': position.longitude,
        'method': 'gps',
        'photo_path': selfie.path,
        'is_mock_gps': position.isMocked,
      });

      setState(() {
        _message = '✅ ${resp.data['message']}';
        _todayLog = resp.data['data'];
      });
    } catch (e) {
      setState(() => _message = 'Gagal: $e');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _checkOut() async {
    setState(() { _loading = true; _message = null; });
    try {
      final position = await _getLocation();
      if (position == null) return;
      final selfie = await _takeSelfie();
      if (selfie == null) return;

      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.post('/attendance/check-out', data: {
        'latitude': position.latitude,
        'longitude': position.longitude,
        'method': 'gps',
        'photo_path': selfie.path,
      });

      setState(() {
        _message = '✅ ${resp.data['message']}';
        _todayLog = resp.data['data'];
      });
    } catch (e) {
      setState(() => _message = 'Gagal: $e');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final checkedIn = _todayLog?['check_in_at'] != null;
    final checkedOut = _todayLog?['check_out_at'] != null;

    return Scaffold(
      appBar: AppBar(title: const Text('Absensi')),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            children: [
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Hari Ini', style: TextStyle(fontSize: 16)),
                          Text(DateTime.now().toString().substring(0, 10), style: const TextStyle(color: Colors.black54)),
                        ],
                      ),
                      const SizedBox(height: 16),
                      _StatusRow(label: 'Check-in', value: _todayLog?['check_in_at']?.toString().substring(11, 16) ?? '–'),
                      _StatusRow(label: 'Check-out', value: _todayLog?['check_out_at']?.toString().substring(11, 16) ?? '–'),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),
              if (!checkedIn)
                ElevatedButton.icon(
                  icon: const Icon(Icons.fingerprint),
                  label: const Text('Clock-in'),
                  onPressed: _loading ? null : _checkIn,
                  style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(56)),
                ),
              if (checkedIn && !checkedOut)
                ElevatedButton.icon(
                  icon: const Icon(Icons.logout),
                  label: const Text('Clock-out'),
                  onPressed: _loading ? null : _checkOut,
                  style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(56), backgroundColor: Colors.red),
                ),
              if (checkedIn && checkedOut)
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(12)),
                  child: const Row(
                    children: [
                      Icon(Icons.check_circle, color: Colors.green),
                      SizedBox(width: 12),
                      Text('Absensi hari ini selesai. Sampai jumpa besok!'),
                    ],
                  ),
                ),
              if (_message != null) ...[
                const SizedBox(height: 16),
                Text(_message!, style: const TextStyle(fontSize: 14)),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class _StatusRow extends StatelessWidget {
  final String label;
  final String value;
  const _StatusRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.black54)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
