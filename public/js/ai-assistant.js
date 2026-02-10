class AiAssistant {
    constructor() {
        this.isOpen = false;
        this.isAuthenticated = false;
        this.remainingQuestions = 3;
        this.isUnlimited = false;
        this.dailyUsed = 0;
        this.dailyLimit = null;
        this.historyLoaded = false;
        this.init();
    }

    init() {
        this.createChatWidget();
        this.attachEventListeners();
        this.checkUserLimit();
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
                            <div class="ai-avatar"></div>
                            <div class="ai-header-text">
                                <h3>Mersy AI Assistant</h3>
                                <p>Online</p>
                            </div>
                        </div>
                        <button class="ai-close-btn" id="aiCloseBtn">&times;</button>
                    </div>

                    <div class="ai-chat-body" id="aiChatBody">
                        <div class="ai-message">
                            <div class="ai-message-avatar robot"></div>
                            <div class="ai-message-content">
                                <div class="ai-message-bubble">
                                    Halo! Aku Mersy AI Assistant 👋 Asisten belajarmu di MersifLab - platform belajar teknologi yang fokus pada IoT, VR, AI, STEM, dan Website Development. Ada yang bisa aku bantu?
                                </div>
                                <div class="ai-message-time">${this.formatTime(new Date())}</div>
                            </div>
                        </div>
                        <div class="ai-suggestions">
                            <button class="ai-suggestion-btn" data-question="Apa itu MersifLab dan apa yang bisa saya pelajari di sini?">
                                Apa itu MersifLab dan apa yang bisa saya pelajari di sini?
                            </button>
                            <button class="ai-suggestion-btn" data-question="Bagaimana cara bergabung dengan program pelatihan di MersifLab?">
                                Bagaimana cara bergabung dengan program pelatihan di MersifLab?
                            </button>
                            <button class="ai-suggestion-btn" data-question="Apa saja layanan dan fitur yang tersedia di LMS MersifLab?">
                                Apa saja layanan dan fitur yang tersedia di LMS MersifLab?
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

        // Suggestion buttons - use event delegation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('ai-suggestion-btn')) {
                const question = e.target.dataset.question;
                if (question) {
                    document.getElementById('aiMessageInput').value = question;
                    this.sendMessage();
                }
            }
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const chatWindow = document.getElementById('aiChatWindow');
        const floatButton = document.getElementById('aiFloatBtn');
        
        chatWindow.classList.toggle('active');
        
        if (this.isOpen) {
            // Load chat history jika belum di-load sebelumnya
            if (!this.historyLoaded) {
                this.loadChatHistory();
            }
            
            // Hide float button when chat is open
            floatButton.style.opacity = '0';
            floatButton.style.pointerEvents = 'none';
            floatButton.style.transform = 'scale(0)';
            
            document.getElementById('aiMessageInput').focus();
            // Scroll ke bawah saat chat dibuka
            this.scrollToBottom();
        } else {
            // Show float button when chat is closed
            setTimeout(() => {
                floatButton.style.opacity = '1';
                floatButton.style.pointerEvents = 'auto';
                floatButton.style.transform = 'scale(1)';
            }, 100);
        }
    }

    async checkUserLimit() {
        try {
            const response = await fetch('/ai-assistant/check-limit');
            const data = await response.json();
            
            this.isAuthenticated = data.is_authenticated;
            this.remainingQuestions = data.remaining_questions;
            this.isUnlimited = data.is_unlimited || false;
            this.dailyUsed = data.daily_used || 0;
            this.dailyLimit = data.daily_limit || null;
            
            this.updateLimitWarning();
        } catch (error) {
            console.error('Error checking limit:', error);
        }
    }

    updateLimitWarning() {
        const warningDiv = document.getElementById('aiLimitWarning');
        const warningText = document.getElementById('aiLimitText');
        
        // Logged-in user with unlimited access
        if (this.isAuthenticated && this.isUnlimited) {
            warningDiv.style.display = 'none';
            return;
        }
        
        // Logged-in user with daily limit
        if (this.isAuthenticated && !this.isUnlimited && this.dailyLimit) {
            if (this.remainingQuestions !== null && this.remainingQuestions <= 5 && this.remainingQuestions > 0) {
                warningDiv.style.display = 'flex';
                warningDiv.classList.remove('error');
                warningText.textContent = `⚠️ ${this.remainingQuestions} pertanyaan tersisa hari ini.`;
            } else if (this.remainingQuestions === 0) {
                warningDiv.style.display = 'flex';
                warningDiv.classList.add('error');
                warningText.textContent = '🔒 Batas harian tercapai. Coba lagi besok!';
            } else {
                warningDiv.style.display = 'none';
            }
            return;
        }
        
        // Guest user
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
                
                // Update unlimited status
                if (data.is_unlimited !== undefined) {
                    this.isUnlimited = data.is_unlimited;
                }
                
                // Update daily usage
                if (data.daily_used !== undefined) {
                    this.dailyUsed = data.daily_used;
                }
            } else if (data.require_login) {
                this.showLoginPrompt();
            } else if (data.daily_limit_reached) {
                this.addMessage(data.message, 'ai');
                this.updateLimitWarning();
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

    addMessage(text, sender, timestamp = null) {
        const chatBody = document.getElementById('aiChatBody');
        const isUser = sender === 'user';
        
        // Format the message content
        const formattedText = isUser ? this.escapeHtml(text) : this.formatAiResponse(text);
        
        // Use provided timestamp or current time
        const time = timestamp ? this.formatTime(new Date(timestamp)) : this.formatTime(new Date());
        
        const avatarClass = isUser ? '' : 'robot';
        
        const messageHtml = `
            <div class="ai-message ${isUser ? 'user' : ''}">
                <div class="ai-message-avatar ${avatarClass}">${isUser ? '' : ''}</div>
                <div class="ai-message-content">
                    <div class="ai-message-bubble">${formattedText}</div>
                    <div class="ai-message-time">${time}</div>
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom(true); // Smooth scroll saat ada message baru
    }

    formatAiResponse(text) {
        // Escape HTML first
        let formatted = this.escapeHtml(text);
        
        // Split into lines for processing
        const lines = formatted.split('\n');
        let result = [];
        let inList = false;
        let listItems = [];
        let listType = null;
        
        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();
            
            if (!line) {
                // Empty line - don't immediately close list
                // Check if next non-empty line is a list item
                let nextNonEmptyLine = null;
                let nextIndex = -1;
                for (let j = i + 1; j < lines.length; j++) {
                    if (lines[j].trim()) {
                        nextNonEmptyLine = lines[j].trim();
                        nextIndex = j;
                        break;
                    }
                }
                
                // Only close list if next non-empty line is NOT a list item
                if (inList && nextNonEmptyLine) {
                    const isNextLineList = nextNonEmptyLine.match(/^\d+[.)]\s+/) || nextNonEmptyLine.match(/^[-*•]\s+/);
                    if (!isNextLineList) {
                        result.push(this.closeList(listItems, listType));
                        listItems = [];
                        inList = false;
                        listType = null;
                    }
                }
                continue;
            }
            
            // Check for horizontal separator (---)
            if (line === '---' || line.match(/^-{3,}$/)) {
                if (inList) {
                    result.push(this.closeList(listItems, listType));
                    listItems = [];
                    inList = false;
                    listType = null;
                }
                result.push('<hr class="ai-separator">');
                continue;
            }
            
            // Check for numbered list
            const numberedMatch = line.match(/^\d+[.)]\s*(.+)$/);
            if (numberedMatch) {
                const content = numberedMatch[1].trim();
                if (content) {
                    if (!inList || listType !== 'numbered') {
                        if (inList) {
                            result.push(this.closeList(listItems, listType));
                            listItems = [];
                        }
                        inList = true;
                        listType = 'numbered';
                    }
                    listItems.push(content);
                    continue;
                }
            }
            
            // Check for bullet points
            const bulletMatch = line.match(/^[-*•]\s+(.+)$/);
            if (bulletMatch) {
                if (!inList || listType !== 'bullet') {
                    if (inList) {
                        result.push(this.closeList(listItems, listType));
                        listItems = [];
                    }
                    inList = true;
                    listType = 'bullet';
                }
                listItems.push(bulletMatch[1]);
                continue;
            }
            
            // Not a list item - check if we should close the list
            if (inList) {
                // Check if next non-empty line is a list item (lookahead)
                let nextNonEmptyLine = null;
                let nextIndex = -1;
                for (let j = i + 1; j < lines.length; j++) {
                    if (lines[j].trim()) {
                        nextNonEmptyLine = lines[j].trim();
                        nextIndex = j;
                        break;
                    }
                }
                
                // If next line is same type of list item, keep list open
                const isNextLineList = nextNonEmptyLine && (nextNonEmptyLine.match(/^\d+[.)]\s+/) || nextNonEmptyLine.match(/^[-*•]\s+/));
                if (!isNextLineList) {
                    result.push(this.closeList(listItems, listType));
                    listItems = [];
                    inList = false;
                    listType = null;
                }
            }
            
            // Check for warning/info messages (starts with emoji)
            if (line.match(/^[⚠️🔒]/)) {
                result.push(`<div class="ai-warning">${line}</div>`);
            }
            // Check for headings
            else if (line.endsWith(':') && line.length < 100) {
                result.push(`<div class="ai-heading">${line}</div>`);
            } 
            // Regular paragraph
            else {
                result.push(`<div class="ai-paragraph">${line}</div>`);
            }
        }
        
        // Close any remaining open list
        if (inList) {
            result.push(this.closeList(listItems, listType));
        }
        
        return result.join('');
    }
    
    closeList(items, type) {
        if (items.length === 0) return '';
        
        const listTag = type === 'numbered' ? 'ol' : 'ul';
        const listItems = items.map(item => `<li>${item}</li>`).join('');
        return `<${listTag} class="ai-list">${listItems}</${listTag}>`;
    }

    showTyping() {
        const chatBody = document.getElementById('aiChatBody');
        const typingHtml = `
            <div class="ai-message typing-indicator">
                <div class="ai-message-avatar robot"></div>
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
        this.scrollToBottom(true);
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
                <div class="ai-message-avatar robot"></div>
                <div class="ai-message-content">
                    <button class="ai-suggestion-btn" onclick="window.location.href='/login'">
                        🔑 Login Sekarang
                    </button>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', loginBtnHtml);
        this.scrollToBottom(true);
    }

    async loadChatHistory() {
        try {
            const response = await fetch('/ai-assistant/history');
            const data = await response.json();
            
            if (data.success && data.chats.length > 0) {
                const chatBody = document.getElementById('aiChatBody');
                // Clear welcome message and suggestions
                chatBody.innerHTML = '';
                
                // Add all previous chats
                data.chats.forEach(chat => {
                    // Add question
                    this.addMessageWithoutScroll(chat.question, 'user', chat.created_at);
                    // Add answer
                    this.addMessageWithoutScroll(chat.answer, 'ai', chat.created_at);
                });
                
                // Scroll ke chat terakhir setelah semua chat dimuat
                // Gunakan requestAnimationFrame untuk memastikan DOM sudah render
                requestAnimationFrame(() => {
                    this.scrollToBottom(false); // Instant scroll, tidak smooth
                });
            }
            
            // Mark history as loaded
            this.historyLoaded = true;
        } catch (error) {
            console.error('Error loading history:', error);
            // Mark as loaded even if error, to prevent infinite retry
            this.historyLoaded = true;
        }
    }

    // Helper method untuk add message tanpa auto-scroll (untuk loading history)
    addMessageWithoutScroll(text, sender, timestamp = null) {
        const chatBody = document.getElementById('aiChatBody');
        const isUser = sender === 'user';
        
        const formattedText = isUser ? this.escapeHtml(text) : this.formatAiResponse(text);
        const time = timestamp ? this.formatTime(new Date(timestamp)) : this.formatTime(new Date());
        const avatarClass = isUser ? '' : 'robot';
        
        const messageHtml = `
            <div class="ai-message ${isUser ? 'user' : ''}">
                <div class="ai-message-avatar ${avatarClass}">${isUser ? '' : ''}</div>
                <div class="ai-message-content">
                    <div class="ai-message-bubble">${formattedText}</div>
                    <div class="ai-message-time">${time}</div>
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', messageHtml);
    }

    // Improved scroll function dengan smooth scroll option
    scrollToBottom(smooth = false) {
        const chatBody = document.getElementById('aiChatBody');
        
        if (smooth) {
            // Smooth scroll untuk UX yang lebih baik
            chatBody.scrollTo({
                top: chatBody.scrollHeight,
                behavior: 'smooth'
            });
        } else {
            // Instant scroll untuk loading history
            chatBody.scrollTop = chatBody.scrollHeight;
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
        return text.replace(/[&<>"']/g, m => map[m]);
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