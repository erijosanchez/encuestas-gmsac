<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción - TRIMAX</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .logo p {
            color: #666;
        }

        .question {
            margin-bottom: 30px;
        }

        .question-text {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        .ratings {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .rating-btn {
            background: #f8f9fa;
            border: 3px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .rating-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .rating-btn.active {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .rating-btn.excellent.active {
            border-color: #4CAF50;
            background: #e8f5e9;
        }

        .rating-btn.good.active {
            border-color: #2196F3;
            background: #e3f2fd;
        }

        .rating-btn.regular.active {
            border-color: #FF9800;
            background: #fff3e0;
        }

        .rating-btn.bad.active {
            border-color: #F44336;
            background: #ffebee;
        }

        .emoji {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .rating-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }

        .submit-btn:hover {
            opacity: 0.9;
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }

        .success-container {
            text-align: center;
        }

        .success-container .emoji {
            font-size: 80px;
            margin: 20px 0;
        }

        .evaluado-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .evaluado-info strong {
            color: #667eea;
            font-size: 18px;
        }

        .evaluado-info small {
            color: #666;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>🔷 TRIMAX</h1>
            <p>Encuesta de Satisfacción</p>
        </div>

        <div id="loading" style="text-align: center; display: none;">
            <p>Cargando encuesta...</p>
        </div>

        <div id="error-container" style="display: none;">
            <div class="alert alert-danger" id="error-message"></div>
        </div>

        <div id="survey-container" style="display: none;">
            <div class="evaluado-info">
                <div>Evaluando a:</div>
                <strong id="evaluado-name"></strong>
                <small id="evaluado-type"></small>
            </div>

            <form id="survey-form">
                <div class="question">
                    <div class="question-text">¿Cómo calificarías tu experiencia en TRIMAX?</div>
                    <div class="ratings">
                        <div class="rating-btn excellent" data-value="4">
                            <div class="emoji">😊</div>
                            <div class="rating-label">Excelente</div>
                        </div>
                        <div class="rating-btn good" data-value="3">
                            <div class="emoji">🙂</div>
                            <div class="rating-label">Bueno</div>
                        </div>
                        <div class="rating-btn regular" data-value="2">
                            <div class="emoji">😐</div>
                            <div class="rating-label">Regular</div>
                        </div>
                        <div class="rating-btn bad" data-value="1">
                            <div class="emoji">😞</div>
                            <div class="rating-label">Malo</div>
                        </div>
                    </div>
                    <input type="hidden" name="experience_rating" id="experience_rating" required>
                </div>

                <div class="form-group">
                    <label>Tu nombre (opcional)</label>
                    <input type="text" name="client_name" id="client_name">
                </div>

                <div class="form-group">
                    <label>Tu email (opcional)</label>
                    <input type="email" name="client_email" id="client_email">
                </div>

                <div class="form-group">
                    <label>Comentarios adicionales (opcional)</label>
                    <textarea name="comments" id="comments" placeholder="Cuéntanos más sobre tu experiencia..."></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">Enviar Encuesta</button>
            </form>
        </div>

        <div id="success-container" class="success-container" style="display: none;">
            <div class="emoji">✅</div>
            <h2 style="color: #2e7d32; margin-bottom: 10px;">¡Gracias por tu opinión!</h2>
            <p style="color: #666;">Tu encuesta ha sido enviada exitosamente.</p>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        let selectedRating = null;

        // Cargar datos de la encuesta
        document.addEventListener('DOMContentLoaded', async () => {
            document.getElementById('loading').style.display = 'block';

            try {
                const response = await fetch(`/api/encuesta/${token}`);
                const data = await response.json();

                if (data.success) {
                    document.getElementById('evaluado-name').textContent = data.data.user.name;
                    document.getElementById('evaluado-type').textContent =
                        data.data.user.role === 'consultor' ? 'Consultor' :
                        `Sede - ${data.data.user.location || ''}`;

                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('survey-container').style.display = 'block';
                } else {
                    showError('Encuesta no encontrada o inactiva');
                }
            } catch (error) {
                showError('Error al cargar la encuesta. Verifica que el servidor esté funcionando.');
            }
        });

        // Manejar selección de rating
        document.querySelectorAll('.rating-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.rating-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedRating = this.dataset.value;
                document.getElementById('experience_rating').value = selectedRating;
            });
        });

        // Enviar formulario
        document.getElementById('survey-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!selectedRating) {
                alert('Por favor selecciona una calificación');
                return;
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            const formData = {
                experience_rating: parseInt(selectedRating),
                client_name: document.getElementById('client_name').value,
                client_email: document.getElementById('client_email').value,
                comments: document.getElementById('comments').value
            };

            try {
                const response = await fetch(`/api/encuesta/${token}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('survey-container').style.display = 'none';
                    document.getElementById('success-container').style.display = 'block';
                } else {
                    showError(data.message || 'Error al enviar la encuesta');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Enviar Encuesta';
                }
            } catch (error) {
                showError('Error de conexión. Intenta nuevamente.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Encuesta';
            }
        });

        function showError(message) {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-container').style.display = 'block';
        }
    </script>
</body>

</html>
