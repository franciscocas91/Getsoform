/**
 * Getso Forms - Form Handler
 */

(function($) {
    'use strict';
    
    // Inicializar todos los formularios
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.getso-form-wrapper');
        forms.forEach(form => new GetsoFormHandler(form));
    });
    
    class GetsoFormHandler {
        constructor(formWrapper) {
            this.formWrapper = formWrapper;
            this.form = formWrapper.querySelector('form');
            this.formId = formWrapper.dataset.formId;
            this.init();
        }
        
        init() {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        
        async handleSubmit(e) {
            e.preventDefault();
            
            // Validar honeypot
            const honeypot = this.form.querySelector('[name="website"]');
            if (honeypot && honeypot.value !== '') {
                return;
            }
            
            // Recopilar datos
            const formData = new FormData(this.form);
            const data = {};
            formData.forEach((value, key) => {
                if (key !== 'website') {
                    data[key] = value;
                }
            });
            
            // Enviar a backend
            try {
                const response = await fetch(getsoForms.ajaxUrl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'getso_forms_save_submission',
                        nonce: getsoForms.nonce,
                        form_id: this.formId,
                        form_data: JSON.stringify(data)
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess();
                    this.form.reset();
                } else {
                    this.showError(result.data.message);
                }
            } catch (error) {
                this.showError('Error de conexión');
            }
        }
        
        showSuccess() {
            const responseDiv = this.form.querySelector('.getso-form-response');
            responseDiv.className = 'getso-form-response success show';
            responseDiv.textContent = '✅ ' + getsoForms.strings.success;
            responseDiv.style.display = 'block';
        }
        
        showError(message) {
            const responseDiv = this.form.querySelector('.getso-form-response');
            responseDiv.className = 'getso-form-response error show';
            responseDiv.textContent = '❌ ' + message;
            responseDiv.style.display = 'block';
        }
    }
})(jQuery);
