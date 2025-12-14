<?php
/**
 * Paciente Model
 * @author  Adianti Clinic SaaS
 */
class Paciente extends TRecord
{
    const TABLENAME = 'paciente';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('data_nascimento');
        parent::addAttribute('genero');
        parent::addAttribute('telefone');
        parent::addAttribute('email');
        parent::addAttribute('endereco');
        parent::addAttribute('observacoes');
        parent::addAttribute('created_at');
    }
}
