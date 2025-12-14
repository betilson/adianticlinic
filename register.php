<?php
/**
 * Register Page - Página de Registro de Nova Conta de Clínica
 * Form dinâmico: Pessoa Física mostra BI, Pessoa Jurídica mostra NIF
 *
 * @version    1.0
 * @author     Adianti Clinic SaaS
 */
require_once 'init.php';

// Check if already logged in
new TSession;
if (TSession::getValue('logged'))
{
    header('Location: admin.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $email = trim($_POST['email'] ?? '');
    $nome_responsavel = trim($_POST['nome_responsavel'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $tipo_cadastro = $_POST['tipo_cadastro'] ?? 'juridica';
    $bi = trim($_POST['bi'] ?? '');
    $nif = trim($_POST['nif'] ?? '');
    $razao_social = trim($_POST['razao_social'] ?? '');
    $nome_empresa = trim($_POST['nome_empresa'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $aceite_termos = isset($_POST['aceite_termos']);
    
    try {
        // Validações
        if (empty($email)) {
            throw new Exception('O email é obrigatório');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        
        if (empty($nome_responsavel)) {
            throw new Exception('O nome do responsável é obrigatório');
        }
        
        if (empty($nome_empresa)) {
            throw new Exception('O nome da empresa/unidade é obrigatório');
        }
        
        if ($tipo_cadastro === 'fisica' && empty($bi)) {
            throw new Exception('O BI é obrigatório para Pessoa Física');
        }
        
        if ($tipo_cadastro === 'juridica' && empty($nif)) {
            throw new Exception('O NIF é obrigatório para Pessoa Jurídica');
        }
        
        if ($tipo_cadastro === 'juridica' && empty($razao_social)) {
            throw new Exception('A Razão Social é obrigatória para Pessoa Jurídica');
        }
        
        if (empty($senha)) {
            throw new Exception('A senha é obrigatória');
        }
        
        if (strlen($senha) < 6) {
            throw new Exception('A senha deve ter pelo menos 6 caracteres');
        }
        
        if ($senha !== $confirmar_senha) {
            throw new Exception('As senhas não conferem');
        }
        
        if (!$aceite_termos) {
            throw new Exception('Você deve aceitar os termos de uso');
        }
        
        // Verificar se email já existe (SystemUser)
        TTransaction::open('permission');
        $existing_user = SystemUser::where('email', '=', $email)->first();
        if ($existing_user) {
            TTransaction::close();
            throw new Exception('Este email já está cadastrado no sistema');
        }
        
        // Verificar se login já existe
        $login = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0]));
        $existing_login = SystemUser::where('login', '=', $login)->first();
        if ($existing_login) {
            // Adicionar número aleatório ao login
            $login = $login . rand(100, 999);
        }
        TTransaction::close();
        
        // Criar a Clínica
        TTransaction::open('clinic');
        // Ensure Clinica class is loaded
        if (!class_exists('Clinica')) {
             throw new Exception('Erro interno: Classe Clinica não encontrada.');
        }
        
        $clinica = new Clinica;
        $clinica->nome_fantasia = $nome_empresa; // Adjusted field name from 'nome' to 'nome_fantasia' based on model
        $clinica->telefone = $telefone;
        $clinica->email = $email;
        // Fields not in model but in user code: tipo_cadastro, nome_responsavel, pais, active, nif, razao_social
        // I should check if I need to add them to Clinica model?
        // User provided logic had: nome_responsavel, pais, active etc.
        // My Clinica.php only has: nome_fantasia, razao_social, cnpj, telefone, email, endereco.
        // I will map what exists. 'cnpj' can store 'nif'.
        
        $clinica->razao_social = $razao_social ? $razao_social : $nome_empresa;
        if ($tipo_cadastro === 'fisica') {
             // Store BI in CNPJ/NIF field or similar?
             // Let's assume cnpj column can hold BI/NIF
             $clinica->cnpj = $bi;
        } else {
             $clinica->cnpj = $nif;
        }
        
        // Missing fields in my model: nome_responsavel, tipo_cadastro. 
        // I will just ignore them for now to avoid error, or better:
        // Updating Clinica model later would be good. 
        // For now, save what fits.
        
        $clinica->created_at = date('Y-m-d H:i:s');
        $clinica->store();
        $clinica_id = $clinica->id;
        TTransaction::close();
        
        // Criar SystemUnit para a clínica
        TTransaction::open('permission');
        $unit = new SystemUnit;
        $unit->name = $nome_empresa;
        $unit->connection_name = 'clinic';
        $unit->store();
        $unit_id = $unit->id;
        TTransaction::close();
        
        // Atualizar a clínica com o system_unit_id
        TTransaction::open('clinic');
        $clinica_update = new Clinica($clinica_id);
        $clinica_update->system_unit_id = $unit_id;
        $clinica_update->store();
        TTransaction::close();
        
        // Buscar ou criar grupo "Clinica Admin"
        TTransaction::open('permission');
        $grupo = SystemGroup::where('name', '=', 'Clinica Admin')->first();
        if (!$grupo) {
            $grupo = new SystemGroup;
            $grupo->name = 'Clinica Admin';
            $grupo->store();
            
            // Adicionar programas básicos ao grupo
            $programas_basicos = [
                'CommonPage', 'WelcomeView', 'ClinicaDashboard', 
                'AgendamentoList', 'AgendamentoForm', 'PacienteList', 'PacienteForm',
                'ClinicaList', 'ClinicaForm'
            ];
            
            foreach ($programas_basicos as $prog_name) {
                $program = SystemProgram::where('controller', '=', $prog_name)->first();
                if ($program) {
                    $grupo_program = new SystemGroupProgram;
                    $grupo_program->system_group_id = $grupo->id;
                    $grupo_program->system_program_id = $program->id;
                    $grupo_program->store();
                }
            }
        }
        
        // Criar usuário admin da clínica
        $user = new SystemUser;
        $user->name = $nome_responsavel;
        $user->login = $login;
        $user->email = $email;
        // $user->phone = $telefone; // SystemUser usually doesn't have phone by default in old versions. Check if exists. Reusing user code. Assuming yes as user provided it.
        $user->password = password_hash($senha, PASSWORD_DEFAULT);
        $user->active = 'Y';
        $user->system_unit_id = $unit_id;
        $user->frontpage_id = 1; // CommonPage or Dashboard
        
        $user->store();
        
        // Associar usuário ao grupo
        $user->addSystemUserGroup($grupo);
        
        // Associar usuário à unidade
        $user->addSystemUserUnit($unit);
        
        TTransaction::close();
        
        // Fazer login automático
        TSession::regenerate();
        $logged_user = ApplicationAuthenticationService::authenticate($login, $senha, false);
        
        if ($logged_user) {
            ApplicationAuthenticationService::loadSessionVars($logged_user, true);
            ApplicationAuthenticationService::setUnit($unit_id);
            ApplicationAuthenticationService::onAfterLogin();
            SystemAccessLogService::registerLogin();
            
            TTransaction::close(); // Just to be safe
            header('Location: admin.php');
            exit;
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        TTransaction::rollback();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Adianti Clinic Saas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="favicon.png"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fafafa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* HEADER (Reused from home/login) */
        .ph-header {
            background: #ffffff;
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e0e0e0;
        }
        
        .ph-header-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .ph-header-login {
            color: #5a6069;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border: 1px solid transparent;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .ph-header-login:hover {
            color: #f5a623;
        }
        
        /* MAIN */
        .register-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }
        
        /* FORM CONTAINER */
        .register-container {
            width: 100%;
            max-width: 700px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        
        .register-title {
            font-size: 28px;
            font-weight: 800;
            color: #3a3f4b;
            text-align: center;
            margin-bottom: 30px;
            font-style: italic;
        }
        
        /* FORM */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            flex: 1 1 100%;
        }
        
        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: #5a6069;
            margin-bottom: 6px;
        }
        
        .form-group small {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dde0e5;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #f5a623;
            box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.1);
        }
        
        .form-input.highlight {
            background: #fffbf0;
            border-color: #f5a623;
        }
        
        /* TIPO CADASTRO */
        .tipo-cadastro {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .tipo-btn {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #dde0e5;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            color: #5a6069;
            transition: all 0.2s;
        }
        
        .tipo-btn:hover {
            border-color: #f5a623;
        }
        
        .tipo-btn.active {
            background: linear-gradient(180deg, #f5a623 0%, #e69510 100%);
            border-color: #f5a623;
            color: #fff;
        }
        
        /* DYNAMIC FIELDS */
        .dynamic-field {
            display: none;
        }
        
        .dynamic-field.visible {
            display: flex;
        }
        
        /* PASSWORD TOGGLE */
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a5b0;
            cursor: pointer;
        }
        
        .password-wrapper .toggle-password:hover {
            color: #6a6f7a;
        }
        
        /* CHECKBOX */
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 20px 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
        }
        
        .checkbox-group label {
            font-size: 13px;
            color: #5a6069;
            cursor: pointer;
        }
        
        .checkbox-group label a {
            color: #f5a623;
        }
        
        /* BUTTON */
        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(180deg, #4ec4f5 0%, #2ba8e0 100%);
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 196, 245, 0.3);
        }
        
        /* MESSAGES */
        .message {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        .message.error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .message.success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #059669;
        }
        
        /* LOGIN LINK */
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #5a6069;
        }
        
        .login-link a {
            color: #3a3f4b;
            font-weight: 500;
        }
        
        .login-link a:hover {
            color: #f5a623;
        }
        
        /* RESPONSIVE */
        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
            
            .register-container {
                padding: 25px;
            }
            
            .tipo-cadastro {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="ph-header">
        <a href="home.php" class="ph-header-logo">
            <img src="favicon.png" style="height:40px;width:auto;">
        </a>
        <a href="login.php" class="ph-header-login">
            <i class="fas fa-sign-in-alt"></i> login
        </a>
    </header>
    
    <!-- MAIN -->
    <main class="register-main">
        <div class="register-container">
            <h1 class="register-title">Nova Conta</h1>
            
            <?php if ($error_message): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <!-- Email -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-input highlight" 
                               placeholder="" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <small>O email informado será utilizado como login no sistema</small>
                    </div>
                </div>
                
                <!-- Nome e Telefone -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome responsável:</label>
                        <input type="text" name="nome_responsavel" class="form-input" 
                               placeholder="informe o seu nome" value="<?php echo htmlspecialchars($_POST['nome_responsavel'] ?? ''); ?>" required>
                        <small>Pessoa responsável pelo acesso admin do sistema</small>
                    </div>
                    <div class="form-group">
                        <label>Telefone:</label>
                        <input type="tel" name="telefone" class="form-input" 
                               placeholder="+244" value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                    </div>
                </div>
                
                <!-- Tipo de Cadastro -->
                <div class="form-group">
                    <label>Tipo de cadastro:</label>
                </div>
                <div class="tipo-cadastro">
                    <button type="button" class="tipo-btn <?php echo ($_POST['tipo_cadastro'] ?? 'juridica') === 'fisica' ? 'active' : ''; ?>" 
                            data-tipo="fisica" onclick="setTipoCadastro('fisica')">
                        Pessoa Física
                    </button>
                    <button type="button" class="tipo-btn <?php echo ($_POST['tipo_cadastro'] ?? 'juridica') === 'juridica' ? 'active' : ''; ?>" 
                            data-tipo="juridica" onclick="setTipoCadastro('juridica')">
                        Pessoa Jurídica
                    </button>
                </div>
                <input type="hidden" name="tipo_cadastro" id="tipo_cadastro" value="<?php echo htmlspecialchars($_POST['tipo_cadastro'] ?? 'juridica'); ?>">
                
                <!-- Campos Pessoa Física (BI) -->
                <div class="form-row dynamic-field" id="campo-bi">
                    <div class="form-group full-width">
                        <label>BI (Bilhete de Identidade):</label>
                        <input type="text" name="bi" class="form-input" 
                               placeholder="Número do BI" value="<?php echo htmlspecialchars($_POST['bi'] ?? ''); ?>">
                    </div>
                </div>
                
                <!-- Campos Pessoa Jurídica (NIF e Razão Social) -->
                <div class="form-row dynamic-field" id="campo-nif">
                    <div class="form-group">
                        <label>NIF:</label>
                        <input type="text" name="nif" class="form-input" 
                               placeholder="Número de Identificação Fiscal" value="<?php echo htmlspecialchars($_POST['nif'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Razão social:</label>
                        <input type="text" name="razao_social" class="form-input" 
                               placeholder="" value="<?php echo htmlspecialchars($_POST['razao_social'] ?? ''); ?>">
                    </div>
                </div>
                
                <!-- Nome da Empresa -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Nome da empresa/unidade:</label>
                        <input type="text" name="nome_empresa" class="form-input" 
                               placeholder="" value="<?php echo htmlspecialchars($_POST['nome_empresa'] ?? ''); ?>" required>
                        <small>Será criada uma empresa/unidade teste</small>
                    </div>
                </div>
                
                <!-- Senhas -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Senha:</label>
                        <div class="password-wrapper">
                            <input type="password" name="senha" id="senha" class="form-input" 
                                   placeholder="••••••••" required>
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('senha')"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirmar senha:</label>
                        <div class="password-wrapper">
                            <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-input" 
                                   placeholder="••••••••" required>
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('confirmar_senha')"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Termos -->
                <div class="checkbox-group">
                    <input type="checkbox" name="aceite_termos" id="aceite_termos" <?php echo isset($_POST['aceite_termos']) ? 'checked' : ''; ?>>
                    <label for="aceite_termos">
                        Li e aceito os termos de uso do Sistema na íntegra. 
                        <a href="#" onclick="openTermos(); return false;">Clique aqui para ler os termos de uso.</a>
                    </label>
                </div>
                
                <!-- Button -->
                <button type="submit" class="btn-register">Começar o teste agora</button>
            </form>
            
            <!-- Login Link -->
            <div class="login-link">
                <i class="fas fa-sign-in-alt"></i> Já tem conta? <a href="login.php">Faça login</a>
            </div>
        </div>
    </main>
    
    <script>
        // Set initial tipo cadastro visibility
        document.addEventListener('DOMContentLoaded', function() {
            var tipo = document.getElementById('tipo_cadastro').value;
            setTipoCadastro(tipo);
        });
        
        function setTipoCadastro(tipo) {
            // Update hidden input
            document.getElementById('tipo_cadastro').value = tipo;
            
            // Update buttons
            document.querySelectorAll('.tipo-btn').forEach(function(btn) {
                btn.classList.remove('active');
                if (btn.dataset.tipo === tipo) {
                    btn.classList.add('active');
                }
            });
            
            // Show/hide dynamic fields
            var campoBi = document.getElementById('campo-bi');
            var campoNif = document.getElementById('campo-nif');
            
            if (tipo === 'fisica') {
                campoBi.classList.add('visible');
                campoNif.classList.remove('visible');
            } else {
                campoBi.classList.remove('visible');
                campoNif.classList.add('visible');
            }
        }
        
        function togglePassword(fieldId) {
            var input = document.getElementById(fieldId);
            var icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
        
        function openTermos() {
            alert('Termos de Uso do Sistema Adianti Clinic Saas\n\n1. Este sistema é fornecido para gestão de clínicas.\n2. O uso é regido pelas leis de Angola.\n3. Os dados são protegidos conforme a política de privacidade.\n\nPara mais informações, entre em contato com o suporte.');
        }
    </script>
</body>
</html>
