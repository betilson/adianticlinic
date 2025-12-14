<?php
/**
 * MovimentacaoEstoque Model
 * @author  Adianti Clinic SaaS
 */
class MovimentacaoEstoque extends TRecord
{
    const TABLENAME = 'movimentacao_estoque';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('material_id');
        parent::addAttribute('tipo');
        parent::addAttribute('quantidade');
        parent::addAttribute('data_movimentacao');
        parent::addAttribute('usuario_id');
        parent::addAttribute('observacao');
    }
}
