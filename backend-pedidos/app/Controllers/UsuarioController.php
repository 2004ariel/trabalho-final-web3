<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    private array $tipos = ['usuario', 'admin', 'superadmin'];

    // ---------------------------------------------------------------------
    // Autenticação (base — Aula 07-08)
    // ---------------------------------------------------------------------

    public function login()
    {
        if (session()->get('usuario')) {
            return redirect()->to('/produtos');
        }

        return view('auth/login', ['titulo' => 'Entrar']);
    }

    public function tentarLogin()
    {
        $email = (string) $this->request->getPost('email');
        $senha = (string) $this->request->getPost('senha');

        $usuarioModel = new UsuarioModel();
        $usuario      = $usuarioModel->porEmail($email);

        if ($usuario === null || ! password_verify($senha, $usuario['senha'])) {
            return redirect()->back()
                             ->withInput()
                             ->with('erro', 'E-mail ou senha inválidos.');
        }

        if ((int) $usuario['bloqueado'] === 1) {
            return redirect()->back()
                             ->withInput()
                             ->with('erro', 'Usuário bloqueado. Entre em contato com o administrador.');
        }

        session()->set('usuario', [
            'id'    => $usuario['id'],
            'nome'  => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo'  => $usuario['tipo'],
        ]);

        return redirect()->to('/produtos')
                         ->with('sucesso', 'Bem-vindo, ' . esc($usuario['nome']) . '!');
    }

    public function logout()
    {
        session()->remove('usuario');

        return redirect()->to('/login')
                         ->with('sucesso', 'Sessão encerrada.');
    }

    // ---------------------------------------------------------------------
    // Parte 3 — Controle de usuários
    // ---------------------------------------------------------------------

    /**
     * Lista todos os usuários (rota protegida por admin:superadmin).
     */
    public function index()
    {
        $usuarioModel = new UsuarioModel();

        return view('admin/usuarios/index', [
            'titulo'   => 'Usuários',
            'usuarios' => $usuarioModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    /**
     * Formulário de criação (rota protegida por admin:superadmin).
     */
    public function criar()
    {
        return view('admin/usuarios/form', [
            'titulo'    => 'Novo usuário',
            'usuario'   => null,
            'tipos'     => $this->tipos,
            'ehEdicao'  => false,
            'podeTipo'  => true, // superadmin chega aqui
        ]);
    }

    /**
     * Salva novo usuário (POST, admin:superadmin).
     */
    public function salvar()
    {
        $regras = [
            'nome'  => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'senha' => 'required|min_length[6]',
        ];

        if (! $this->validate($regras)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $tipo = $this->request->getPost('tipo');
        if (! in_array($tipo, $this->tipos, true)) {
            $tipo = 'usuario';
        }

        $usuarioModel = new UsuarioModel();
        $usuarioModel->insert([
            'nome'      => $this->request->getPost('nome'),
            'email'     => $this->request->getPost('email'),
            'senha'     => password_hash((string) $this->request->getPost('senha'), PASSWORD_DEFAULT),
            'tipo'      => $tipo,
            'bloqueado' => 0,
        ]);

        return redirect()->to('/admin/usuarios')
                         ->with('sucesso', 'Usuário cadastrado com sucesso.');
    }

    /**
     * Formulário de edição.
     * Superadmin edita qualquer um; usuário comum só o próprio registro.
     */
    public function editar($id)
    {
        $logado = session()->get('usuario');
        if (! $logado) {
            return redirect()->to('/login')->with('erro', 'Faça login para continuar.');
        }

        $ehSuperadmin = ($logado['tipo'] ?? '') === 'superadmin';

        if (! $ehSuperadmin && (int) $logado['id'] !== (int) $id) {
            return redirect()->to('/produtos')
                             ->with('erro', 'Você só pode editar o seu próprio cadastro.');
        }

        $usuarioModel = new UsuarioModel();
        $usuario      = $usuarioModel->find($id);

        if ($usuario === null) {
            return redirect()->to($ehSuperadmin ? '/admin/usuarios' : '/produtos')
                             ->with('erro', 'Usuário não encontrado.');
        }

        return view('admin/usuarios/form', [
            'titulo'   => 'Editar usuário',
            'usuario'  => $usuario,
            'tipos'    => $this->tipos,
            'ehEdicao' => true,
            'podeTipo' => $ehSuperadmin,
        ]);
    }

    /**
     * Atualiza dados (POST). Senha só é alterada se preenchida.
     */
    public function atualizar($id)
    {
        $logado = session()->get('usuario');
        if (! $logado) {
            return redirect()->to('/login')->with('erro', 'Faça login para continuar.');
        }

        $ehSuperadmin = ($logado['tipo'] ?? '') === 'superadmin';

        if (! $ehSuperadmin && (int) $logado['id'] !== (int) $id) {
            return redirect()->to('/produtos')
                             ->with('erro', 'Você só pode editar o seu próprio cadastro.');
        }

        $usuarioModel = new UsuarioModel();
        $usuario      = $usuarioModel->find($id);
        if ($usuario === null) {
            return redirect()->to($ehSuperadmin ? '/admin/usuarios' : '/produtos')
                             ->with('erro', 'Usuário não encontrado.');
        }

        $regras = [
            'nome'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
        ];
        if ($this->request->getPost('senha')) {
            $regras['senha'] = 'min_length[6]';
        }

        if (! $this->validate($regras)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'  => $this->request->getPost('nome'),
            'email' => $this->request->getPost('email'),
        ];

        // Tipo só pode ser alterado por superadmin
        if ($ehSuperadmin) {
            $tipo = $this->request->getPost('tipo');
            if (in_array($tipo, $this->tipos, true)) {
                $dados['tipo'] = $tipo;
            }
        }

        // Senha só muda se preenchida
        if ($this->request->getPost('senha')) {
            $dados['senha'] = password_hash((string) $this->request->getPost('senha'), PASSWORD_DEFAULT);
        }

        $usuarioModel->update($id, $dados);

        // Mantém a sessão coerente se o usuário editou a si mesmo
        if ((int) $logado['id'] === (int) $id) {
            $logado['nome']  = $dados['nome'];
            $logado['email'] = $dados['email'];
            if (isset($dados['tipo'])) {
                $logado['tipo'] = $dados['tipo'];
            }
            session()->set('usuario', $logado);
        }

        $destino = $ehSuperadmin ? '/admin/usuarios' : '/produtos';

        return redirect()->to($destino)
                         ->with('sucesso', 'Dados atualizados com sucesso.');
    }

    /**
     * Alterna o bloqueio do usuário (0 ↔ 1). (admin:superadmin)
     */
    public function bloquear($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuario      = $usuarioModel->find($id);

        if ($usuario === null) {
            return redirect()->to('/admin/usuarios')
                             ->with('erro', 'Usuário não encontrado.');
        }

        $logado = session()->get('usuario');
        if ($logado && (int) $logado['id'] === (int) $id) {
            return redirect()->to('/admin/usuarios')
                             ->with('erro', 'Você não pode bloquear o seu próprio usuário.');
        }

        $novo = (int) $usuario['bloqueado'] === 1 ? 0 : 1;
        $usuarioModel->update($id, ['bloqueado' => $novo]);

        $msg = $novo === 1 ? 'Usuário bloqueado.' : 'Usuário desbloqueado.';

        return redirect()->to('/admin/usuarios')->with('sucesso', $msg);
    }
}
