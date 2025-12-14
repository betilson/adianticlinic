<?php
/**
 * Clinica Model
 * @author  Adianti Clinic SaaS
 */
class Clinica extends TRecord
{
    const TABLENAME = 'clinica';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('razao_social');
        parent::addAttribute('cnpj');
        parent::addAttribute('telefone');
        parent::addAttribute('email');
        parent::addAttribute('endereco');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }
}
