<?php
/**
 * Material Model
 * @author  Adianti Clinic SaaS
 */
class Material extends TRecord
{
    const TABLENAME = 'material';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('nome');
        parent::addAttribute('unidade');
        parent::addAttribute('estoque_minimo');
        parent::addAttribute('estoque_atual');
        parent::addAttribute('preco_custo');
    }
}
