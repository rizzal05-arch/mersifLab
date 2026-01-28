class AiAssistant {
    constructor() {
        this.isOpen = false;
        this.isAuthenticated = false;
        this.remainingQuestions = 3;
        this.init();
    }

    init() {
        this.createChatWidget();
        this.attachEventListeners();
        this.checkUserLimit();
        this.loadChatHistory();
    }

    createChatWidget() {
        const html = `
            <div class="ai-assistant-container">
                <button class="ai-float-button" id="aiFloatBtn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 3 .97 4.29L2 22l5.71-.97C9 21.64 10.46 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.38 0-2.69-.29-3.88-.81l-.28-.14-2.89.49.49-2.89-.14-.28C4.29 14.69 4 13.38 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.59 8-8 8z"/>
                        <circle cx="9" cy="12" r="1.5"/>
                        <circle cx="15" cy="12" r="1.5"/>
                    </svg>
                    <span class="ai-notification-badge" id="aiNotificationBadge" style="display: none;">AI</span>
                </button>

                <div class="ai-chat-window" id="aiChatWindow">
                    <div class="ai-chat-header">
                        <div class="ai-header-info">
                            <div class="ai-avatar">AI</div>
                            <div class="ai-header-text">
                                <h3>Mersi AI Assistant</h3>
                                <p>Online</p>
                            </div>
                        </div>
                        <button class="ai-close-btn" id="aiCloseBtn">&times;</button>
                    </div>

                    <div class="ai-chat-body" id="aiChatBody">
                        <div class="ai-message">
                            <div class="ai-message-avatar">AI</div>
                            <div class="ai-message-content">
                                <div class="ai-message-bubble">
                                    Halo! Saya Mersi AI Assistant. Siap membantu Anda belajar teknologi AI, IoT, VR, dan STEM. Ada yang bisa saya bantu?
                                </div>
                                <div class="ai-message-time">${this.formatTime(new Date())}</div>
                            </div>
                        </div>
                        <div class="ai-suggestions">
                            <button class="ai-suggestion-btn" data-question="Bagaimana cara memulai belajar Machine Learning?">
                                Bagaimana cara memulai belajar Machine Learning?
                            </button>
                            <button class="ai-suggestion-btn" data-question="Apa perbedaan antara IoT dan AI?">
                                Apa perbedaan antara IoT dan AI?
                            </button>
                            <button class="ai-suggestion-btn" data-question="Bagaimana membuat aplikasi VR sederhana?">
                                Bagaimana membuat aplikasi VR sederhana?
                            </button>
                        </div>
                    </div>

                    <div class="ai-chat-footer">
                        <div class="ai-limit-warning" id="aiLimitWarning" style="display: none;">
                            ⚠️ <span id="aiLimitText"></span>
                        </div>
                        <div class="ai-input-wrapper">
                            <textarea 
                                id="aiMessageInput" 
                                placeholder="Tanyakan sesuatu..." 
                                rows="1"
                            ></textarea>
                            <button class="ai-send-btn" id="aiSendBtn">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', html);
    }

    attachEventListeners() {
        document.getElementById('aiFloatBtn').addEventListener('click', () => this.toggleChat());
        document.getElementById('aiCloseBtn').addEventListener('click', () => this.toggleChat());
        document.getElementById('aiSendBtn').addEventListener('click', () => this.sendMessage());
        
        const input = document.getElementById('aiMessageInput');
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Suggestion buttons
        document.querySelectorAll('.ai-suggestion-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const question = e.target.dataset.question;
                document.getElementById('aiMessageInput').value = question;
                this.sendMessage();
            });
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const chatWindow = document.getElementById('aiChatWindow');
        chatWindow.classList.toggle('active');
        
        if (this.isOpen) {
            document.getElementById('aiMessageInput').focus();
        }
    }

    async checkUserLimit() {
        try {
            const response = await fetch('/ai-assistant/check-limit');
            const data = await response.json();
            
            this.isAuthenticated = data.is_authenticated;
            this.remainingQuestions = data.remaining_questions;
            
            this.updateLimitWarning();
        } catch (error) {
            console.error('Error checking limit:', error);
        }
    }

    updateLimitWarning() {
        const warningDiv = document.getElementById('aiLimitWarning');
        const warningText = document.getElementById('aiLimitText');
        
        if (!this.isAuthenticated && this.remainingQuestions !== null) {
            if (this.remainingQuestions > 0) {
                warningDiv.style.display = 'flex';
                warningDiv.classList.remove('error');
                warningText.textContent = `Anda memiliki ${this.remainingQuestions} pertanyaan tersisa. Login untuk unlimited!`;
            } else {
                warningDiv.style.display = 'flex';
                warningDiv.classList.add('error');
                warningText.innerHTML = 'Batas pertanyaan tercapai. <a href="/login" style="color: #721c24; font-weight: bold;">Login sekarang</a>';
            }
        } else {
            warningDiv.style.display = 'none';
        }
    }

    async sendMessage() {
        const input = document.getElementById('aiMessageInput');
        const message = input.value.trim();
        
        if (!message) return;

        // Check limit
        if (!this.isAuthenticated && this.remainingQuestions <= 0) {
            this.showLoginPrompt();
            return;
        }

        // Clear input
        input.value = '';
        input.style.height = 'auto';

        // Add user message
        this.addMessage(message, 'user');

        // Show typing indicator
        this.showTyping();

        // Disable send button
        const sendBtn = document.getElementById('aiSendBtn');
        sendBtn.disabled = true;

        try {
            const response = await fetch('/ai-assistant/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            // Remove typing indicator
            this.hideTyping();

            if (data.success) {
                this.addMessage(data.answer, 'ai');
                
                // Update remaining questions
                if (data.remaining_questions !== null && data.remaining_questions !== undefined) {
                    this.remainingQuestions = data.remaining_questions;
                    this.updateLimitWarning();
                }
            } else if (data.require_login) {
                this.showLoginPrompt();
            } else {
                this.addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'ai');
            }
        } catch (error) {
            console.error('Error:', error);
            this.hideTyping();
            this.addMessage('Maaf, terjadi kesalahan koneksi. Silakan coba lagi.', 'ai');
        } finally {
            sendBtn.disabled = false;
        }
    }

    addMessage(text, sender) {
        const chatBody = document.getElementById('aiChatBody');
        const isUser = sender === 'user';
        
        const messageHtml = `
            <div class="ai-message ${isUser ? 'user' : ''}">
                <div class="ai-message-avatar">${isUser ? 'U' : 'AI'}</div>
                <div class="ai-message-content">
                    <div class="ai-message-bubble">${this.escapeHtml(text)}</div>
                    <div class="ai-message-time">${this.formatTime(new Date())}</div>
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', messageHtml);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    showTyping() {
        const chatBody = document.getElementById('aiChatBody');
        const typingHtml = `
            <div class="ai-message typing-indicator">
                <div class="ai-message-avatar">AI</div>
                <div class="ai-message-content">
                    <div class="ai-typing">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', typingHtml);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    hideTyping() {
        const typingIndicator = document.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    showLoginPrompt() {
        this.addMessage('Anda telah mencapai batas 3 pertanyaan. Silakan login untuk melanjutkan percakapan dengan saya. 🔐', 'ai');
        
        const chatBody = document.getElementById('aiChatBody');
        const loginBtnHtml = `
            <div class="ai-message">
                <div class="ai-message-avatar">AI</div>
                <div class="ai-message-content">
                    <button class="ai-suggestion-btn" onclick="window.location.href='/login'">
                        🔑 Login Sekarang
                    </button>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', loginBtnHtml);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    async loadChatHistory() {
        try {
            const response = await fetch('/ai-assistant/history');
            const data = await response.json();
            
            if (data.success && data.chats.length > 0) {
                // Load previous chats
                // You can implement this if needed
            }
        } catch (error) {
            console.error('Error loading history:', error);
        }
    }

    formatTime(date) {
        return date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]).replace(/\n/g, '<br>');
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new AiAssistant();
    });
} else {
    new AiAssistant();
}