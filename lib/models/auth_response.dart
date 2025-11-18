import 'user.dart';

class AuthResponse {
  final bool success;
  final String? message;
  final String? token;
  final User? user;
  final String? error;

  AuthResponse({
    required this.success,
    this.message,
    this.token,
    this.user,
    this.error,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) {
    return AuthResponse(
      success: json['success'] as bool,
      message: json['message'] as String?,
      token: json['token'] as String?,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      error: json['error'] as String?,
    );
  }
}
