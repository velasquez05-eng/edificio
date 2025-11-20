/**
 * Agente Chat - Componente de chat con Gemini AI
 * Integrado en el sistema de gestión de edificio
 */

class AgenteChat {
    constructor() {
        this.isOpen = false;
        this.historial = [];
        this.apiUrl = '../controlador/AgenteControlador.php';
        this.chatContainer = null;
        this.messagesContainer = null;
        this.inputElement = null;
        this.sendButton = null;
        this.toggleButton = null;
        this.isLoading = false;

        this.init();
    }

    init() {
        this.createChatHTML();
        this.loadInfoSistema();
        this.setupEventListeners();
    }

    createChatHTML() {
        // Crear botón flotante para abrir el chat
        const toggleBtn = document.createElement('button');
        toggleBtn.id = 'agente-chat-toggle';
        toggleBtn.className = 'agente-chat-toggle';
        toggleBtn.innerHTML = '<i class="fas fa-robot"></i>';
        toggleBtn.setAttribute('aria-label', 'Abrir asistente virtual');
        document.body.appendChild(toggleBtn);
        this.toggleButton = toggleBtn;

        // Crear contenedor del chat
        const chatContainer = document.createElement('div');
        chatContainer.id = 'agente-chat-container';
        chatContainer.className = 'agente-chat-container';
        chatContainer.innerHTML = `
            <div class="agente-chat-header">
                <div class="agente-chat-header-info">
                    <div class="agente-chat-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="agente-chat-header-text">
                        <h5>Asistente Virtual</h5>
                        <span class="agente-chat-status">En línea</span>
                    </div>
                </div>
                <button class="agente-chat-close" id="agente-chat-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="agente-chat-messages" id="agente-chat-messages">
                <div class="agente-chat-welcome" id="agente-chat-welcome">
                    <div class="agente-chat-welcome-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <p id="agente-welcome-message">Cargando...</p>
                    <div class="agente-chat-suggestions" id="agente-chat-suggestions">
                        <!-- Las preguntas sugeridas se cargarán aquí -->
                    </div>
                </div>
            </div>
            <div class="agente-chat-input-container">
                <div class="agente-chat-input-wrapper">
                    <input 
                        type="text" 
                        id="agente-chat-input" 
                        class="agente-chat-input" 
                        placeholder="Escribe tu mensaje..."
                        autocomplete="off"
                    >
                    <button id="agente-chat-send" class="agente-chat-send-btn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <small class="agente-chat-hint">Presiona Enter para enviar</small>
            </div>
        `;
        document.body.appendChild(chatContainer);
        
        this.chatContainer = chatContainer;
        this.messagesContainer = document.getElementById('agente-chat-messages');
        this.inputElement = document.getElementById('agente-chat-input');
        this.sendButton = document.getElementById('agente-chat-send');
    }

    loadInfoSistema() {
        fetch(this.apiUrl + '?action=obtenerInfoSistema')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.mensaje_bienvenida) {
                    const welcomeMsg = document.getElementById('agente-welcome-message');
                    if (welcomeMsg) {
                        welcomeMsg.textContent = data.mensaje_bienvenida;
                    }
                }
                
                // Cargar preguntas sugeridas
                if (data.success && data.preguntas_sugeridas && data.preguntas_sugeridas.length > 0) {
                    this.mostrarPreguntasSugeridas(data.preguntas_sugeridas);
                }
            })
            .catch(error => {
                console.error('Error al cargar información del sistema:', error);
                const welcomeMsg = document.getElementById('agente-welcome-message');
                if (welcomeMsg) {
                    welcomeMsg.textContent = '¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte?';
                }
            });
    }

    mostrarPreguntasSugeridas(preguntas) {
        const suggestionsContainer = document.getElementById('agente-chat-suggestions');
        if (!suggestionsContainer) return;

        suggestionsContainer.innerHTML = '<div class="agente-suggestions-title">Preguntas sugeridas:</div>';
        
        preguntas.forEach((pregunta, index) => {
            const suggestionBtn = document.createElement('button');
            suggestionBtn.className = 'agente-suggestion-btn';
            suggestionBtn.textContent = pregunta;
            suggestionBtn.addEventListener('click', () => {
                this.inputElement.value = pregunta;
                this.inputElement.focus();
                this.sendButton.disabled = false;
            });
            suggestionsContainer.appendChild(suggestionBtn);
        });
    }

    setupEventListeners() {
        // Toggle del chat
        this.toggleButton.addEventListener('click', () => this.toggleChat());
        
        // Cerrar chat
        const closeBtn = document.getElementById('agente-chat-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeChat());
        }

        // Enviar mensaje
        this.sendButton.addEventListener('click', () => this.sendMessage());
        
        // Enviar con Enter
        this.inputElement.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!this.isLoading && this.inputElement.value.trim()) {
                    this.sendMessage();
                }
            }
        });

        // Habilitar/deshabilitar botón según input
        this.inputElement.addEventListener('input', () => {
            const hasText = this.inputElement.value.trim().length > 0;
            this.sendButton.disabled = !hasText || this.isLoading;
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.openChat();
        } else {
            this.closeChat();
        }
    }

    openChat() {
        this.chatContainer.classList.add('agente-chat-open');
        this.toggleButton.classList.add('agente-chat-toggle-open');
        this.inputElement.focus();
    }

    closeChat() {
        this.chatContainer.classList.remove('agente-chat-open');
        this.toggleButton.classList.remove('agente-chat-toggle-open');
    }

    async sendMessage() {
        const mensaje = this.inputElement.value.trim();
        
        if (!mensaje || this.isLoading) {
            return;
        }

        // Limpiar el input
        this.inputElement.value = '';
        this.sendButton.disabled = true;
        this.isLoading = true;

        // Remover mensaje de bienvenida y sugerencias si existen
        const welcomeDiv = this.messagesContainer.querySelector('.agente-chat-welcome');
        if (welcomeDiv) {
            welcomeDiv.remove();
        }

        // Agregar mensaje del usuario
        this.addMessage('user', mensaje);

        // Agregar mensaje de "escribiendo..."
        const typingId = this.addTypingIndicator();

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'enviarMensaje',
                    mensaje: mensaje,
                    historial: this.historial
                })
            });

            // Remover indicador de escritura
            this.removeTypingIndicator(typingId);

            // Verificar si la respuesta es OK
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error HTTP:', response.status, errorText);
                throw new Error(`Error del servidor: ${response.status}`);
            }

            const data = await response.json();

            // Verificar si hay error en la respuesta
            if (!data || typeof data !== 'object') {
                throw new Error('Respuesta inválida del servidor');
            }

            if (data.success && data.respuesta) {
                // Agregar respuesta del agente
                this.addMessage('assistant', data.respuesta, data.accion, data.url);
                
                // Guardar en historial
                this.historial.push({
                    mensaje: mensaje,
                    respuesta: data.respuesta
                });

                // Limitar historial a los últimos 10 intercambios
                if (this.historial.length > 10) {
                    this.historial.shift();
                }
                
                // El botón de acción ya se agrega en addMessage si hay data.accion y data.url
            } else {
                // Mostrar error con detalles si están disponibles
                let mensajeError = data.respuesta || 'Lo siento, hubo un error al procesar tu mensaje.';
                
                // Siempre loguear errores en consola para debugging
                if (data.error) {
                    console.error('Error del agente:', data.error);
                }
                
                this.addMessage('assistant', mensajeError);
            }
        } catch (error) {
            console.error('Error al enviar mensaje:', error);
            this.removeTypingIndicator(typingId);
            
            let mensajeError = 'Lo siento, hubo un error de conexión. ';
            if (error.message && error.message.includes('Failed to fetch')) {
                mensajeError += 'Por favor, verifica tu conexión a internet e intenta nuevamente.';
            } else if (error.message && error.message.includes('Error del servidor')) {
                mensajeError += 'El servidor está experimentando problemas. Por favor, intenta más tarde.';
            } else {
                mensajeError += 'Por favor, intenta nuevamente.';
            }
            
            this.addMessage('assistant', mensajeError);
        } finally {
            this.isLoading = false;
            this.sendButton.disabled = false;
            this.inputElement.focus();
        }
    }

    addMessage(role, text, accion = null, url = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `agente-chat-message agente-chat-message-${role}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'agente-chat-message-content';
        
        if (role === 'assistant') {
            const avatar = document.createElement('div');
            avatar.className = 'agente-chat-message-avatar';
            avatar.innerHTML = '<i class="fas fa-robot"></i>';
            messageDiv.appendChild(avatar);
        }
        
        const textDiv = document.createElement('div');
        textDiv.className = 'agente-chat-message-text';
        textDiv.textContent = text;
        
        // Formatear texto (detectar URLs y rutas)
        this.formatMessageText(textDiv);
        
        messageContent.appendChild(textDiv);
        
        // Agregar botón de acción si existe
        if (role === 'assistant' && accion && url) {
            const actionBtn = document.createElement('button');
            actionBtn.className = 'agente-action-btn';
            actionBtn.innerHTML = this.obtenerTextoBoton(accion);
            actionBtn.onclick = () => {
                window.location.href = url;
            };
            messageContent.appendChild(actionBtn);
        }
        
        if (role === 'user') {
            messageDiv.appendChild(messageContent);
        } else {
            messageDiv.appendChild(messageContent);
        }
        
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    obtenerTextoBoton(accion) {
        // Texto del botón según la acción
        if (accion.includes('Factura')) {
            if (accion.includes('verMis')) {
                return '<i class="fas fa-file-invoice me-2"></i>Ver Mis Facturas';
            }
            return '<i class="fas fa-file-invoice me-2"></i>Ver Facturas';
        } else if (accion.includes('Reserva')) {
            if (accion.includes('formulario')) {
                return '<i class="fas fa-calendar-plus me-2"></i>Hacer Reserva';
            }
            return '<i class="fas fa-calendar-check me-2"></i>Ver Mis Reservas';
        } else if (accion.includes('Incidente')) {
            if (accion.includes('formulario')) {
                return '<i class="fas fa-exclamation-circle me-2"></i>Reportar Incidente';
            } else if (accion.includes('verMis') || accion.includes('verIncidentesAsignados')) {
                return '<i class="fas fa-clipboard-list me-2"></i>Ver Mis Incidentes';
            }
            return '<i class="fas fa-clipboard-list me-2"></i>Ver Incidentes';
        } else if (accion.includes('HistorialPagos')) {
            return '<i class="fas fa-history me-2"></i>Ver Historial de Pagos';
        } else if (accion.includes('Concepto')) {
            return '<i class="fas fa-tags me-2"></i>Ver Mis Conceptos';
        } else if (accion.includes('Comunicado')) {
            return '<i class="fas fa-bullhorn me-2"></i>Ver Comunicados';
        } else if (accion.includes('Perfil')) {
            return '<i class="fas fa-user me-2"></i>Ver Mi Perfil';
        } else if (accion.includes('Persona')) {
            if (accion.includes('formulario')) {
                return '<i class="fas fa-user-plus me-2"></i>Registrar Persona';
            }
            return '<i class="fas fa-users me-2"></i>Ver Personal';
        } else if (accion.includes('Departamento')) {
            if (accion.includes('formulario')) {
                return '<i class="fas fa-building me-2"></i>Crear Departamento';
            }
            return '<i class="fas fa-building me-2"></i>Ver Departamentos';
        } else if (accion.includes('Planilla')) {
            return '<i class="fas fa-file-alt me-2"></i>Ver Mi Planilla';
        }
        return '<i class="fas fa-external-link-alt me-2"></i>Abrir';
    }

    formatMessageText(element) {
        const text = element.textContent;
        const html = text
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
            .replace(/\*([^*]+)\*/g, '<em>$1</em>')
            .replace(/\.\.\/controlador\/([^\s]+)/g, '<code class="route-path">../controlador/$1</code>')
            .replace(/action=([^\s&]+)/g, '<code class="action-code">action=$1</code>')
            .replace(/\n/g, '<br>');
        
        element.innerHTML = html;
    }

    addTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'agente-chat-message agente-chat-message-assistant agente-chat-typing';
        typingDiv.id = 'agente-typing-indicator';
        
        const avatar = document.createElement('div');
        avatar.className = 'agente-chat-message-avatar';
        avatar.innerHTML = '<i class="fas fa-robot"></i>';
        typingDiv.appendChild(avatar);
        
        const content = document.createElement('div');
        content.className = 'agente-chat-message-content';
        
        const typingDots = document.createElement('div');
        typingDots.className = 'agente-chat-typing-dots';
        typingDots.innerHTML = '<span></span><span></span><span></span>';
        content.appendChild(typingDots);
        
        typingDiv.appendChild(content);
        this.messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
        
        return 'agente-typing-indicator';
    }

    removeTypingIndicator(id) {
        const indicator = document.getElementById(id);
        if (indicator) {
            indicator.remove();
        }
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

// Inicializar el chat cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.agenteChat = new AgenteChat();
    });
} else {
    window.agenteChat = new AgenteChat();
}

