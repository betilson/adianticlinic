<?php
/**
 * ContaReceber Model
 * @author  Adianti Clinic SaaS
 */
class ContaReceber extends TRecord
{
    const TABLENAME = 'conta_receber';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('paciente_id');
        parent::addAttribute('categoria_id');
        parent::addAttribute('descricao');
        parent::addAttribute('valor');
        parent::addAttribute('data_emissao');
        parent::addAttribute('data_vencimento');
        parent::addAttribute('data_pagamento');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
    }
}
