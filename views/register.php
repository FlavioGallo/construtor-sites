<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Construtor de Sites</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .divider {
            text-align: center;
            margin: 20px 0;
            color: #999;
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            color: #667eea;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
        .password-strength-bar.weak { width: 33%; background: #e74c3c; }
        .password-strength-bar.medium { width: 66%; background: #f39c12; }
        .password-strength-bar.strong { width: 100%; background: #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Criar Conta</h1>
        <p class="subtitle">Comece a construir seu site agora</p>
        
        <div id="message"></div>
        
        <form id="registerForm">
            <div class="form-group">
                <label for="name">Nome completo</label>
                <input type="text" id="name" name="name" required placeholder="João Silva">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirmar senha</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Repita a senha">
            </div>
            
            <button type="submit">Criar conta</button>
        </form>
        
        <div class="divider">ou</div>
        
        <div class="link">
            <a href="/login">Já tenho uma conta</a>
        </div>
    </div>

    <script>
        // Verificar força da senha
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
        
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                confirmPassword: document.getElementById('confirmPassword').value
            };
            
            if (formData.password !== formData.confirmPassword) {
                showMessage('❌ As senhas não coincidem', 'error');
                return;
            }
            
            if (formData.password.length < 6) {
                showMessage('❌ A senha deve ter pelo menos 6 caracteres', 'error');
                return;
            }
            
            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('✅ Conta criada com sucesso! Redirecionando...', 'success');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1000);
                } else {
                    showMessage('❌ ' + result.error, 'error');
                }
            } catch (error) {
                showMessage('❌ Erro de conexão: ' + error.message, 'error');
            }
        });
        
        function showMessage(text, type) {
            const msgDiv = document.getElementById('message');
            msgDiv.className = type;
            msgDiv.textContent = text;
        }
    </script>
</body>
</html>
