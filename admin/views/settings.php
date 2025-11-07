<?php
/**
 * Vista: Configuración
 */

if (!defined('ABSPATH')) {
    exit;
}

// Guardar configuración
if (isset($_POST['getso_save_settings']) && check_admin_referer('getso_settings_nonce')) {
    // IA
    update_option('getso_forms_ai_provider', sanitize_text_field($_POST['ai_provider'] ?? 'claude'));
    update_option('getso_forms_ai_api_key', sanitize_text_field($_POST['ai_api_key'] ?? ''));
    update_option('getso_forms_ai_model', sanitize_text_field($_POST['ai_model'] ?? 'claude-sonnet-4-20250514'));
    update_option('getso_forms_ai_requests_per_hour', intval($_POST['ai_requests_per_hour'] ?? 10));
    
    // General
    update_option('getso_forms_max_forms', intval($_POST['max_forms'] ?? 20));
    
    echo '<div class="notice notice-success"><p>✅ Configuración guardada</p></div>';
}

// Obtener valores actuales
$ai_provider = get_option('getso_forms_ai_provider', 'claude');
$ai_api_key = get_option('getso_forms_ai_api_key', '');
$ai_model = get_option('getso_forms_ai_model', 'claude-sonnet-4-20250514');
$ai_requests = get_option('getso_forms_ai_requests_per_hour', 10);
$max_forms = get_option('getso_forms_max_forms', 20);
?>

<div class="wrap">
    <h1>⚙️ Configuración de Getso Forms</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('getso_settings_nonce'); ?>
        
        <h2 class="nav-tab-wrapper">
            <a href="#ai" class="nav-tab nav-tab-active">🤖 IA CSS Generator</a>
            <a href="#general" class="nav-tab">⚙️ General</a>
        </h2>
        
        <!-- TAB IA -->
        <div id="ai" class="tab-content">
            <h2>🤖 Configuración del Asistente IA</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Proveedor de IA</th>
                    <td>
                        <label>
                            <input type="radio" name="ai_provider" value="claude" <?php checked($ai_provider, 'claude'); ?>>
                            Anthropic Claude (Recomendado)
                        </label><br>
                        
                        <label>
                            <input type="radio" name="ai_provider" value="openai" <?php checked($ai_provider, 'openai'); ?>>
                            OpenAI (GPT-4)
                        </label><br>
                        
                        <label>
                            <input type="radio" name="ai_provider" value="gemini" <?php checked($ai_provider, 'gemini'); ?>>
                            Google Gemini
                        </label>
                        
                        <p class="description">Selecciona el proveedor de IA para generar CSS</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="password" name="ai_api_key" value="<?php echo esc_attr($ai_api_key); ?>" class="regular-text" id="ai-api-key-input">
                        <button type="button" class="button" id="toggle-api-key">👁️ Mostrar</button>
                        
                        <p class="description">
                            <strong>Claude:</strong> Obtén tu API key en <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a><br>
                            <strong>OpenAI:</strong> Obtén tu API key en <a href="https://platform.openai.com/" target="_blank">platform.openai.com</a><br>
                            <strong>Gemini:</strong> Obtén tu API key en <a href="https://makersuite.google.com/" target="_blank">makersuite.google.com</a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Modelo</th>
                    <td>
                        <select name="ai_model" id="ai-model-select">
                            <optgroup label="Claude (Anthropic)">
                                <option value="claude-sonnet-4-20250514" <?php selected($ai_model, 'claude-sonnet-4-20250514'); ?>>Claude Sonnet 4 (Recomendado)</option>
                                <option value="claude-opus-4-20250514" <?php selected($ai_model, 'claude-opus-4-20250514'); ?>>Claude Opus 4 (Más preciso)</option>
                                <option value="claude-3-5-sonnet-20241022" <?php selected($ai_model, 'claude-3-5-sonnet-20241022'); ?>>Claude 3.5 Sonnet</option>
                            </optgroup>
                            
                            <optgroup label="OpenAI">
                                <option value="gpt-4o" <?php selected($ai_model, 'gpt-4o'); ?>>GPT-4o</option>
                                <option value="gpt-4-turbo" <?php selected($ai_model, 'gpt-4-turbo'); ?>>GPT-4 Turbo</option>
                                <option value="gpt-4" <?php selected($ai_model, 'gpt-4'); ?>>GPT-4</option>
                            </optgroup>
                            
                            <optgroup label="Google Gemini">
                                <option value="gemini-pro" <?php selected($ai_model, 'gemini-pro'); ?>>Gemini Pro</option>
                                <option value="gemini-pro-vision" <?php selected($ai_model, 'gemini-pro-vision'); ?>>Gemini Pro Vision</option>
                            </optgroup>
                        </select>
                        
                        <p class="description">Modelo de IA a utilizar</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Límite de Solicitudes</th>
                    <td>
                        <input type="number" name="ai_requests_per_hour" value="<?php echo esc_attr($ai_requests); ?>" min="1" max="100" class="small-text">
                        solicitudes por hora
                        
                        <p class="description">Máximo de solicitudes a la IA por hora (para controlar costos)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Test de Conexión</th>
                    <td>
                        <button type="button" class="button" id="test-ai-connection">🔍 Probar Conexión</button>
                        <div id="ai-test-result" style="margin-top: 10px;"></div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- TAB GENERAL -->
        <div id="general" class="tab-content" style="display:none;">
            <h2>⚙️ Configuración General</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Máximo de Formularios</th>
                    <td>
                        <input type="number" name="max_forms" value="<?php echo esc_attr($max_forms); ?>" min="1" max="100" class="small-text">
                        formularios
                        
                        <p class="description">Número máximo de formularios que se pueden crear</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Versión del Plugin</th>
                    <td>
                        <strong><?php echo GETSO_FORMS_VERSION; ?></strong>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <button type="submit" name="getso_save_settings" class="button button-primary button-large">💾 Guardar Configuración</button>
        </p>
    </form>
</div>

<style>
.tab-content {
    margin-top: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Sistema de tabs
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.tab-content').hide();
        $(target).show();
    });
    
    // Toggle API key visibility
    $('#toggle-api-key').on('click', function() {
        const input = $('#ai-api-key-input');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(this).text('🙈 Ocultar');
        } else {
            input.attr('type', 'password');
            $(this).text('👁️ Mostrar');
        }
    });
    
    // Test de conexión IA
    $('#test-ai-connection').on('click', function() {
        const $btn = $(this);
        const $result = $('#ai-test-result');
        
        $btn.prop('disabled', true).text('⏳ Probando...');
        $result.html('');
        
        $.post(ajaxurl, {
            action: 'getso_forms_test_ai_connection',
            nonce: getsoFormsAdmin.nonce
        }, function(response) {
            $btn.prop('disabled', false).text('🔍 Probar Conexión');
            
            if (response.success) {
                $result.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
            } else {
                $result.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
            }
        });
    });
});
</script>
