<?php
class TNrOrdemValidator extends TFieldValidator
{
	public function validate($label, $value, $params = NULL)
	{
        $len = strlen($value);
        if(strlen(_NIVEISPC_) == 6 && $len == 1 OR $len == 3 OR $len == 6) 
        {
            TTransaction::open(_DATABASE_);         	
            if($len == 6)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                	throw new Exception("Conta grupo (<strong>X</strong>.x.xx) não existe neste Plano de Contas");

                $cod = substr($value, 0, 3);
            	$repository = new TRepository('Finplanoconta');
            	$count = $repository->where('ordem', '=', $cod)->count();
            	if($count == 0) 
            	    throw new Exception("Conta Subgrupo (x.<strong>X</strong>.xx) não existe neste Plano de Contas");
            }
            elseif($len == 3)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0)
                    throw new Exception("Conta Grupo (<strong>X</strong>.x.xx) não existe neste Plano de Contas");
            }
            TTransaction::close();
        }

        elseif(strlen(_NIVEISPC_) == 7 && $len == 1 OR $len == 4 OR $len == 7) 
        {
            TTransaction::open(_DATABASE_);             
            if($len == 7)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta grupo (<strong>X</strong>.xx.xx) não existe neste Plano de Contas");

                $cod = substr($value, 0, 4);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta Subgrupo (x.<strong>XX</strong>.xx) não existe neste Plano de Contas");
            }
            elseif($len == 4)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0)
                    throw new Exception("Conta Grupo (<strong>X</strong>.xx.xx) nãoxxx existe neste Plano de Contas");
            }
            TTransaction::close();
        }


        elseif(strlen(_NIVEISPC_) == 8 && $len == 1 OR $len == 3 OR $len == 5 OR $len == 8)
        { 
            TTransaction::open(_DATABASE_);             
            if($len == 8)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta Grupo Geral (<strong>X</strong>.x.x.xx) não existe neste Plano de Contas");

                $cod = substr($value, 0, 3);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta Grupo (x.<strong>X</strong>.x.xx) não existe neste Plano de Contas");

                $cod = substr($value, 0, 5);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta Subgrupo (x.x.<strong>X</strong>.xx) não existe neste Plano de Contas");
            }
            elseif($len == 5)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta Grupo Geral (<strong>X</strong>.x.x.xx) não existe neste Plano de Contas");

                $cod = substr($value, 0, 3);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0) 
                    throw new Exception("Conta Grupo (x.<strong>X</strong>.x.xx) não existe neste Plano de Contas");
            }
            elseif($len == 3)
            {
                $cod = substr($value, 0, 1);
                $repository = new TRepository('Finplanoconta');
                $count = $repository->where('ordem', '=', $cod)->count();
                if($count == 0)
                    throw new Exception("Conta Grupo Geral (<strong>X</strong>.x.x.xx) não existe neste Plano de Contas");
            }
            TTransaction::close();
        }
        else
            throw new Exception("Nº de ordem inválido !");
	}
}