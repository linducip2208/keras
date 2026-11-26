import 'package:dio/dio.dart';
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
  List<dynamic> _history = [];
  String? _historyError;

  @override
  void initState() {
    super.initState();
    _loadToday();
  }

  Future<void> _loadToday() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.get('/attendance/today');
      if (!mounted) return;
      setState(() => _todayLog = resp.data['data']);
      _loadHistory();
    } catch (_) {
      if (!mounted) return;
      setState(() => _todayLog = null);
    }
  }

  Future<void> _loadHistory() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.get('/attendance/logs');
      if (!mounted) return;
      setState(() {
        _history = (resp.data['data'] as List?) ?? [];
        _historyError = null;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _historyError = 'Riwayat gagal dimuat.');
    }
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
      locationSettings: const LocationSettings(
        accuracy: LocationAccuracy.high,
        timeLimit: Duration(seconds: 20),
      ),
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

  /// Upload selfie ke server, kembalikan server path.
  /// Backend menolak path lokal HP — wajib lewat endpoint ini.
  Future<String> _uploadSelfie(XFile selfie) async {
    final dio = ref.read(apiClientProvider).dio;
    final form = FormData.fromMap({
      'photo': await MultipartFile.fromFile(selfie.path, filename: 'selfie.jpg'),
    });
    final resp = await dio.post('/attendance/photo', data: form);
    return resp.data['photo_path'] as String;
  }

  String _friendlyError(Object e) {
    if (e is DioException) {
      final data = e.response?.data;
      if (data is Map) {
        final msg = data['error'] ?? data['message'];
        if (msg != null) return msg.toString();
      }
      if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.receiveTimeout) {
        return 'Koneksi lambat. Coba lagi.';
      }
      return 'Jaringan bermasalah. Coba lagi.';
    }
    return 'Terjadi kesalahan: $e';
  }

  Future<void> _checkIn() async {
    setState(() { _loading = true; _message = null; });
    try {
      final position = await _getLocation();
      if (position == null) {
        setState(() => _message = 'GPS tidak tersedia. Nyalakan lokasi & izinkan akses.');
        return;
      }
      final selfie = await _takeSelfie();
      if (selfie == null) {
        setState(() => _message = 'Selfie dibatalkan.');
        return;
      }
      final photoPath = await _uploadSelfie(selfie);

      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.post('/attendance/check-in', data: {
        'latitude': position.latitude,
        'longitude': position.longitude,
        'method': 'gps',
        'photo_path': photoPath,
        'is_mock_gps': position.isMocked,
      });

      setState(() {
        _message = '✅ ${resp.data['message']}';
        _todayLog = resp.data['data'];
      });
      _loadHistory();
    } catch (e) {
      setState(() => _message = _friendlyError(e));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _checkOut() async {
    setState(() { _loading = true; _message = null; });
    try {
      final position = await _getLocation();
      if (position == null) {
        setState(() => _message = 'GPS tidak tersedia. Nyalakan lokasi & izinkan akses.');
        return;
      }
      final selfie = await _takeSelfie();
      if (selfie == null) {
        setState(() => _message = 'Selfie dibatalkan.');
        return;
      }
      final photoPath = await _uploadSelfie(selfie);

      final dio = ref.read(apiClientProvider).dio;
      final resp = await dio.post('/attendance/check-out', data: {
        'latitude': position.latitude,
        'longitude': position.longitude,
        'method': 'gps',
        'photo_path': photoPath,
        'is_mock_gps': position.isMocked,
      });

      setState(() {
        _message = '✅ ${resp.data['message']}';
        _todayLog = resp.data['data'];
      });
      _loadHistory();
    } catch (e) {
      setState(() => _message = _friendlyError(e));
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
                      _StatusRow(label: 'Check-in', value: _hhmm(_todayLog?['check_in_at']?.toString() ?? '')),
                      _StatusRow(label: 'Check-out', value: _hhmm(_todayLog?['check_out_at']?.toString() ?? '')),
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
              const SizedBox(height: 20),
              const Align(
                alignment: Alignment.centerLeft,
                child: Text('Riwayat 30 Hari', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(height: 8),
              if (_historyError != null)
                Text(_historyError!, style: const TextStyle(color: Colors.black54)),
              if (_history.isEmpty && _historyError == null)
                const Text('Belum ada riwayat.', style: TextStyle(color: Colors.black54)),
              Expanded(
                child: ListView.builder(
                  itemCount: _history.length,
                  itemBuilder: (_, i) {
                    final h = (_history[i] as Map<String, dynamic>);
                    final ci = (h['check_in_at'] ?? '-').toString();
                    final co = (h['check_out_at'] ?? '-').toString();
                    final t = (h['status'] ?? '').toString();
                    return Card(
                      child: ListTile(
                        title: Text((h['date'] ?? '-').toString().substring(0, 10)),
                        subtitle: Text('In: ${_hhmm(ci)}  •  Out: ${_hhmm(co)}'),
                        trailing: t.isEmpty ? null : Chip(
                          label: Text(t, style: const TextStyle(fontSize: 11)),
                          visualDensity: VisualDensity.compact,
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

String _hhmm(String v) => v.length >= 16 ? v.substring(11, 16) : '–';

class _StatusRow extends StatelessWidget {  final String label;
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
