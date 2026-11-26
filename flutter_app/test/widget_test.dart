// Smoke test: aplikasi bisa di-build dan menampilkan AuthGate
// (Login atau loading state, tergantung token tersimpan).

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:hris_mobile/main.dart';

void main() {
  testWidgets('App boots ke AuthGate', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: HrisApp()));
    await tester.pump();

    // AuthGate menampilkan loading ATAU LoginScreen — keduanya valid.
    expect(
      find.byType(CircularProgressIndicator).evaluate().isNotEmpty ||
          find.text('Masuk').evaluate().isNotEmpty,
      isTrue,
    );
  });
}
