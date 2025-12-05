<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de satisfacción - TRIMAX</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: 'TRIMAX';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 250px;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.03);
            letter-spacing: 20px;
            z-index: 0;
            pointer-events: none;
        }

        .container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .header {
            background: #1565C0;
            color: white;
            text-align: center;
            padding: 20px;
            margin: -50px -40px 30px -40px;
            border-radius: 20px 20px 0 0;
            font-size: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .evaluado-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
            border: 2px solid #e0e0e0;
        }

        .evaluado-info .label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .evaluado-info .name {
            color: #1565C0;
            font-size: 22px;
            font-weight: 700;
            margin: 5px 0;
        }

        .evaluado-info .type {
            color: #888;
            font-size: 13px;
        }

        .question {
            margin-bottom: 40px;
        }

        .question-text {
            font-size: 16px;
            font-weight: 700;
            color: #1565C0;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ratings {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .rating-btn {
            background: white;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .rating-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .rating-btn.active.muy-feliz {
            border-color: #4CAF50;
            background: #E8F5E9;
        }

        .rating-btn.active.feliz {
            border-color: #2196F3;
            background: #E3F2FD;
        }

        .rating-btn.active.insatisfecho {
            border-color: #FF9800;
            background: #FFF3E0;
        }

        .rating-btn.active.muy-insatisfecho {
            border-color: #F44336;
            background: #FFEBEE;
        }

        .emoji {
            font-size: 50px;
            margin-bottom: 10px;
            display: block;
        }

        .rating-label {
            font-size: 12px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1565C0;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: #1a1a2e;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: #16213e;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .trimax-logo {
            text-align: center;
            margin-top: 30px;
        }

        .trimax-logo svg {
            width: 150px;
            height: auto;
        }

        .success-container {
            text-align: center;
            display: none;
        }

        .success-container.show {
            display: block;
        }

        .success-emoji {
            font-size: 80px;
            margin: 20px 0;
        }

        .success-container h2 {
            color: #4CAF50;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .success-container p {
            color: #666;
            font-size: 16px;
        }

        .loading {
            text-align: center;
            padding: 50px;
            display: none;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #1565C0;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            .header {
                margin: -30px -20px 20px -20px;
                padding: 15px;
                font-size: 16px;
            }

            .ratings {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .emoji {
                font-size: 40px;
            }

            .rating-label {
                font-size: 11px;
            }

            body::before {
                font-size: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading show" id="loading">
            <div class="spinner"></div>
            <p>Cargando encuesta...</p>
        </div>

        <div id="survey-container" style="display: none;">
            <div class="header">Encuesta de satisfacción - TRIMAX</div>

            <div class="evaluado-info">
                <div class="label">Evaluando a:</div>
                <div class="name" id="evaluado-name"></div>
                <div class="type" id="evaluado-type"></div>
            </div>

            <form id="survey-form">
                <!-- Pregunta 1: Experiencia general -->
                <div class="question">
                    <div class="question-text">¿Cómo calificarías tu experiencia en TRIMAX?</div>
                    <div class="ratings">
                        <div class="rating-btn muy-feliz" data-question="1" data-value="4">
                            <span class="emoji">😊</span>
                            <div class="rating-label">Muy<br>Feliz</div>
                        </div>
                        <div class="rating-btn feliz" data-question="1" data-value="3">
                            <span class="emoji">🙂</span>
                            <div class="rating-label">Feliz</div>
                        </div>
                        <div class="rating-btn insatisfecho" data-question="1" data-value="2">
                            <span class="emoji">😐</span>
                            <div class="rating-label">Insatisfecho</div>
                        </div>
                        <div class="rating-btn muy-insatisfecho" data-question="1" data-value="1">
                            <span class="emoji">😞</span>
                            <div class="rating-label">Muy<br>Insatisfecho</div>
                        </div>
                    </div>
                    <input type="hidden" name="experience_rating" id="experience_rating" required>
                </div>

                <!-- Pregunta 2: Atención (cambia según tipo) -->
                <div class="question">
                    <div class="question-text" id="service-quality-question">
                        ¿Cómo evaluarías la atención y el soporte de tu Consultor Trimax?
                    </div>
                    <div class="ratings">
                        <div class="rating-btn muy-feliz" data-question="2" data-value="4">
                            <span class="emoji">😊</span>
                            <div class="rating-label">Muy<br>Feliz</div>
                        </div>
                        <div class="rating-btn feliz" data-question="2" data-value="3">
                            <span class="emoji">🙂</span>
                            <div class="rating-label">Feliz</div>
                        </div>
                        <div class="rating-btn insatisfecho" data-question="2" data-value="2">
                            <span class="emoji">😐</span>
                            <div class="rating-label">Insatisfecho</div>
                        </div>
                        <div class="rating-btn muy-insatisfecho" data-question="2" data-value="1">
                            <span class="emoji">😞</span>
                            <div class="rating-label">Muy<br>Insatisfecho</div>
                        </div>
                    </div>
                    <input type="hidden" name="service_quality_rating" id="service_quality_rating" required>
                </div>

                <div class="form-group">
                    <label>Tu nombre (opcional)</label>
                    <input type="text" name="client_name" id="client_name" placeholder="Escribe tu nombre aquí...">
                </div>

                <div class="form-group">
                    <label>Cuéntanos brevemente qué podríamos mejorar o qué te gustó más de nuestra atención.</label>
                    <textarea name="comments" id="comments" placeholder="Tu opinión es anónima y nos ayuda a mejorar..."></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">ENVIAR</button>

                <div class="trimax-logo">
                    <svg viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg">
                        <text x="10" y="35" font-family="Arial, sans-serif" font-size="32" font-weight="900" fill="#1565C0">TRIMAX</text>
                    </svg>
                </div>
            </form>
        </div>

        <div class="success-container" id="success-container">
            <div class="success-emoji">✅</div>
            <h2>¡Gracias por tu opinión!</h2>
            <p>Tu encuesta ha sido enviada exitosamente.</p>
            <div class="trimax-logo">
                <svg viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg">
                    <text x="10" y="35" font-family="Arial, sans-serif" font-size="32" font-weight="900" fill="#4CAF50">TRIMAX</text>
                </svg>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        let selectedRatings = {
            experience: null,
            service_quality: null
        };
        let userRole = null;
        let formStarted = false;

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch(`/api/encuesta/${token}`);
                const data = await response.json();

                if (data.success) {
                    userRole = data.data.user.role;
                    
                    document.getElementById('evaluado-name').textContent = data.data.user.name;
                    document.getElementById('evaluado-type').textContent =
                        userRole === 'consultor' ? 'Consultor' :
                        `Sede - ${data.data.user.location || ''}`;

                    const serviceQualityQuestion = document.getElementById('service-quality-question');
                    if (userRole === 'consultor') {
                        serviceQualityQuestion.textContent = '¿Cómo evaluarías la atención y el soporte de tu Consultor Trimax?';
                    } else {
                        serviceQualityQuestion.textContent = '¿Cómo evaluarías la atención y el soporte de tu Sede Trimax?';
                    }

                    document.getElementById('loading').classList.remove('show');
                    document.getElementById('survey-container').style.display = 'block';
                } else {
                    alert('Encuesta no encontrada o inactiva');
                }
            } catch (error) {
                alert('Error al cargar la encuesta');
            }
        });

        document.querySelectorAll('.rating-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                formStarted = true;
                const question = this.dataset.question;
                
                document.querySelectorAll(`.rating-btn[data-question="${question}"]`).forEach(b => {
                    b.classList.remove('active');
                });
                
                this.classList.add('active');
                
                if (question === '1') {
                    selectedRatings.experience = this.dataset.value;
                    document.getElementById('experience_rating').value = selectedRatings.experience;
                } else if (question === '2') {
                    selectedRatings.service_quality = this.dataset.value;
                    document.getElementById('service_quality_rating').value = selectedRatings.service_quality;
                }
            });
        });

        document.getElementById('client_name').addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                formStarted = true;
            }
        });

        document.getElementById('comments').addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                formStarted = true;
            }
        });

        document.getElementById('survey-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!selectedRatings.experience || !selectedRatings.service_quality) {
                alert('Por favor selecciona una calificación para ambas preguntas');
                return;
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            const formData = {
                experience_rating: parseInt(selectedRatings.experience),
                service_quality_rating: parseInt(selectedRatings.service_quality),
                client_name: document.getElementById('client_name').value,
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
                    formStarted = false;
                    document.getElementById('survey-container').style.display = 'none';
                    document.getElementById('success-container').classList.add('show');
                } else {
                    alert(data.message || 'Error al enviar la encuesta');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'ENVIAR';
                }
            } catch (error) {
                alert('Error de conexión. Intenta nuevamente.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'ENVIAR';
            }
        });

        window.addEventListener('beforeunload', (e) => {
            if (formStarted && document.getElementById('survey-container').style.display !== 'none') {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>
