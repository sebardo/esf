<?php
    use_helper('Debug');
    use_helper('Date', 'Number');

    $tmpDir = sfConfig::get('sf_upload_dir') . '/tmp';
    if (!is_dir($tmpDir)) {
        mkdir($tmpDir, 0777);
        chmod($tmpDir, 0777);
    }
    $fname = tempnam($tmpDir, 'export');

    $workbook = &new writeexcel_workbook($fname);
    $workbook->set_tempdir($tmpDir);
    $worksheet = &$workbook->addworksheet();

    // Set the columns width

    $worksheet->set_column(0, 0, 13);
    $worksheet->set_column(1, 1, 30);
    $worksheet->set_column(2, 2, 30);
    $worksheet->set_column(3, 3, 24);
    $worksheet->set_column(4, 4, 13);
    $worksheet->set_column(5, 5, 30);
    $worksheet->set_column(6, 6, 20);
    $worksheet->set_column(7, 7, 10);
    $worksheet->set_column(8, 14, 10);
    $worksheet->set_column(14, 15, 20);

    // Create formats

    $border1 =& $workbook->addformat();
    $border1->set_color('black');
    $border1->set_bold();
    $border1->set_size(15);
    $border1->set_pattern(0x1);
    $border1->set_fg_color('white');
    $border1->set_top(6);
    $border1->set_bottom(6);
    $border1->set_left(6);
    $border1->set_align('center');
    $border1->set_align('vcenter');
    $border1->set_merge(); # This is the key feature

    $border2 =& $workbook->addformat();
    $border2->set_color('black');
    $border2->set_bold();
    $border2->set_size(15);
    $border2->set_pattern(0x1);
    $border2->set_fg_color('white');
    $border2->set_top(6);
    $border2->set_bottom(6);
    $border2->set_right(6);
    $border2->set_align('center');
    $border2->set_align('vcenter');
    $border2->set_merge(); # This is the key feature

    $border3 =& $workbook->addformat();
    $border3->set_color('black');
    $border3->set_bold();
    $border3->set_size(12);
    $border3->set_pattern(0x1);
    $border3->set_fg_color('white');
    $border3->set_top(6);
    $border3->set_bottom(6);
    $border3->set_right(0);
    $border3->set_align('center');
    $border3->set_align('vcenter');
    $border3->set_merge(); # This is the key feature

    $center =& $workbook->addformat();
    $center->set_align('center');
    $center->set_size(10);

    $header =& $workbook->addformat();
    $header->set_color('black');
    $header->set_align('left');
    $header->set_align('vcenter');
    $header->set_size(10);
    $header->set_pattern();
    $header->set_fg_color('white');
    $header->set_border_color('black');
    $header->set_top(0);
    $header->set_bottom(2);
    $header->set_left(1);
    $header->set_pattern(0x1);
    $header->set_text_wrap(1);
    $header->set_merge(); # This is the key feature

    $esquerra = $workbook->addformat();
    $esquerra->set_align('left');
    $esquerra->set_size(10);

    $dreta = $workbook->addformat();
    $dreta->set_align('right');
    $dreta->set_size(10);

    $bgColorChild = 'aqua';

    $child = $workbook->addformat();
    $child->set_align('center');
    $child->set_size(10);
    $child->set_bg_color($bgColorChild);

    $childRed = $workbook->addformat();
    $childRed->set_bold();
    $childRed->set_color('red');
    $childRed->set_align('center');
    $childRed->set_size(10);
    $childRed->set_bg_color($bgColorChild);

    $childGreen = $workbook->addformat();
    $childGreen->set_bold();
    $childGreen->set_color('green');
    $childGreen->set_align('center');
    $childGreen->set_size(10);
    $childGreen->set_bg_color($bgColorChild);

    $parentRed = $workbook->addformat();
    $parentRed->set_bold();
    $parentRed->set_color('red');
    $parentRed->set_align('center');
    $parentRed->set_size(10);

    $parentGreen = $workbook->addformat();
    $parentGreen->set_bold();
    $parentGreen->set_color('green');
    $parentGreen->set_align('center');
    $parentGreen->set_size(10);

    // CABECERA
    $col = 0;
    foreach ($columns as $key => $value) {
        if (in_array($key, $selectedColumns)) {
            $worksheet->write(0, $col, utf8_decode($value), $header);
            $col++;
        }
    }

    $row = 2;

    foreach ($inscriptionsGrouped as $inscription) {
        $col = 0;
        if (in_array('father_dni', $selectedColumns)) {
            $worksheet->write($row, $col, $inscription['father_dni'], $center);
            $col++;
        }

        if (in_array('father_name', $selectedColumns)) {
            $worksheet->write($row, $col, utf8_decode($inscription['father_name']), $center);
            $col++;
        }
        
        if (in_array('father_mail', $selectedColumns)) {
            $worksheet->write($row, $col, utf8_decode($inscription['father_mail']), $center);
            $col++;
        }
        
        if (in_array('phones', $selectedColumns)) {
            $worksheet->write($row, $col, utf8_decode($inscription['phones']), $center);
            $col++;
        }

        if ($inscription['NUM_REG'] == 1) 
        {
            if (in_array('student_name', $selectedColumns)) {
                $worksheet->write($row, $col, utf8_decode($inscription['student_name']), $center);
                $col++;
            }
            
            if (in_array('inscription_code', $selectedColumns)) {
                $worksheet->write($row, $col, $inscription['inscription_code'], $center);
                $col++;
            }

            if (in_array('week_title', $selectedColumns)) {
                $worksheet->write($row, $col, utf8_decode($inscription['week_title']), $center);
                $col++;
            }
            
            if (in_array('method_payment', $selectedColumns)) {
                switch ($inscription['method_payment']) {
                    case InscriptionPeer::METHOD_PAYMENT_CASH:
                        $worksheet->write($row, $col, __('Pago en efectivo'), $center);
                        break;
                    case InscriptionPeer::METHOD_PAYMENT_RECIBO:
                        $worksheet->write($row, $col, __('Recibo domiciliado'), $center);
                        break;
                    case InscriptionPeer::METHOD_PAYMENT_TRANSFER:
                        $worksheet->write($row, $col, __('Transferencia bancaria'), $center);
                        break;
                    default:
                        $worksheet->write($row, $col, '', $center);
                }
                $col++;
            }
        } 
        else {
            if (in_array('student_name', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }

            if (in_array('inscription_code', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }
            
            if (in_array('week_title', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }
            
            if (in_array('method_payment', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }
        }

        if (in_array('IMPORTE_DESCUENTO', $selectedColumns)) {
            $worksheet->write($row, $col, $inscription['IMPORTE_DESCUENTO'], $center);
            $col++;
        }

        if (in_array('IMPORTE_DESCUENTO_PORCENTAJE', $selectedColumns))
        {
            if ($inscription['IMPORTE_DESCUENTO_PORCENTAJE'] > 0) {
                $worksheet->write($row, $col, $inscription['IMPORTE_DESCUENTO_PORCENTAJE'] . ' %', $center);
            }
            elseif ($inscription['IMPORTE_DESCUENTO_CANTIDAD'] > 0) {
                $worksheet->write($row, $col, $inscription['IMPORTE_DESCUENTO_CANTIDAD'] . ' €', $center);
            }
            else {
                $worksheet->write($row, $col, 0, $center);
            }

            $col++;
        }
        
        if (in_array('IMPORTE_BECA', $selectedColumns)) {
            $worksheet->write($row, $col, $inscription['IMPORTE_BECA'], $center);
            $col++;
        }
        
        if (in_array('IMPORTE_PRIMER_PAGO', $selectedColumns)) {
            $worksheet->write($row, $col, $inscription['IMPORTE_PRIMER_PAGO'], $center);
            $col++;
        }
        
        if (in_array('IMPORTE_SEGUNDO_PAGO', $selectedColumns)) {
            $worksheet->write($row, $col, $inscription['IMPORTE_SEGUNDO_PAGO'], $center);
            $col++;
        }

        if ($inscription['NUM_REG'] == 1)
        {
            if (in_array('IMPORTE_TOTAL_A_PAGAR', $selectedColumns)) {
                $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_A_PAGAR'], $center);
                $col++;
            }

            if (in_array('IMPORTE_TOTAL_INSCRIPCION', $selectedColumns)) {
                $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_A_PAGAR'], $center);
                $col++;
            }

            if (in_array('IMPORTE_TOTAL_PENDIENTE', $selectedColumns)) {
                if ($insc['IMPORTE_TOTAL_PENDIENTE'] > 0) {
                    $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_PENDIENTE'], $parentRed);
                    $col++;
                    $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_PENDIENTE'], $parentRed);
                } else {
                    $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_PENDIENTE'], $parentGreen);
                    $col++;
                    $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_PENDIENTE'], $parentGreen);
                }
            }
            
            if (in_array('first_payment_date', $selectedColumns)) {
                $worksheet->write($row, $col, $inscription['first_payment_date'], $center);
                $col++;
            }
            
            if (in_array('second_payment_date', $selectedColumns)) {
                $worksheet->write($row, $col, $inscription['second_payment_date'], $center);
                $col++;
            }
        }
        else {
            if (in_array('IMPORTE_TOTAL_A_PAGAR', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }

            if (in_array('IMPORTE_TOTAL_INSCRIPCION', $selectedColumns)) {
                $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_A_PAGAR'], $center);
                $col++;
            }

            if (in_array('IMPORTE_TOTAL_PENDIENTE', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }
            
            if (in_array('IMPORTE_TOTAL_PENDIENTE_INSCRIPCION', $selectedColumns)) {
                if ($inscription['IMPORTE_TOTAL_PENDIENTE'] > 0) {
                    $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_PENDIENTE'], $parentRed);
                } else {
                    $worksheet->write($row, $col, $inscription['IMPORTE_TOTAL_PENDIENTE'], $parentGreen);
                }
            }

            if (in_array('first_payment_date', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }
            
            if (in_array('second_payment_date', $selectedColumns)) {
                $worksheet->write($row, $col, '', $center);
                $col++;
            }
        }

        $row++;

        
        if ($inscription['NUM_REG'] > 1) {
            foreach ($inscriptionsNotGrouped as $insc)
            {
                if ($insc['inscription_num'] == $inscription['inscription_num'])
                {
                    $col = 0;
                    $visibleCols = 0;
                    if (in_array('father_dni', $selectedColumns)) {
                        $worksheet->write($row, $col, $inscription['father_dni'], $child);
                        $col++;
                    }

                    if (in_array('father_name', $selectedColumns)) {
                        $worksheet->write($row, $col, utf8_decode($insc['father_name']), $child);
                        $col++;
                    }
                    
                    if (in_array('father_mail', $selectedColumns)) {
                        $worksheet->write($row, $col, utf8_decode($insc['father_mail']), $child);
                        $col++;
                    }

                    if (in_array('phones', $selectedColumns)) {
                        $worksheet->write($row, $col, utf8_decode($insc['phones']), $child);
                        $col++;
                    }
                    
                    if (in_array('student_name', $selectedColumns)) {
                        $worksheet->write($row, $col, utf8_decode($insc['student_name']), $child);
                        $col++;
                    }
                    
                    if (in_array('inscription_code', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['inscription_code'], $child);
                        $col++;
                    }

                    if (in_array('week_title', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['week_title'], $child);
                        $col++;
                    }
                    
                    if (in_array('method_payment', $selectedColumns)) {
                        switch ($insc['method_payment']) {
                            case InscriptionPeer::METHOD_PAYMENT_CASH:
                                $worksheet->write($row, $col, __('Pago en efectivo'), $child);
                                break;
                            case InscriptionPeer::METHOD_PAYMENT_RECIBO:
                                $worksheet->write($row, $col, __('Recibo domiciliado'), $child);
                                break;
                            case InscriptionPeer::METHOD_PAYMENT_TRANSFER:
                                $worksheet->write($row, $col, __('Transferencia bancaria'), $child);
                                break;
                            default:
                                $worksheet->write($row, $col, '', $child);
                        }
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_DESCUENTO', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['IMPORTE_DESCUENTO'], $child);
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_DESCUENTO_PORCENTAJE', $selectedColumns))
                    {
                        if ($insc['IMPORTE_DESCUENTO_PORCENTAJE'] > 0) {
                            $worksheet->write($row, $col, $insc['IMPORTE_DESCUENTO_PORCENTAJE'] . ' %', $child);
                        }
                        elseif ($insc['IMPORTE_DESCUENTO_CANTIDAD'] > 0) {
                            $worksheet->write($row, $col, $insc['IMPORTE_DESCUENTO_CANTIDAD'] . ' €', $child);
                        }
                        else {
                            $worksheet->write($row, $col, 0, $center);
                        }
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_BECA', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['IMPORTE_BECA'], $child);
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_PRIMER_PAGO', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['IMPORTE_PRIMER_PAGO'], $child);
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_SEGUNDO_PAGO', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['IMPORTE_SEGUNDO_PAGO'], $child);
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_TOTAL_A_PAGAR', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['IMPORTE_TOTAL_A_PAGAR'], $child);
                        $col++;
                    }
                    
                    if (in_array('IMPORTE_TOTAL_INSCRIPCION', $selectedColumns)) {
                        $worksheet->write($row, $col, '', $child);
                        $col++;
                    }

                    if (in_array('IMPORTE_TOTAL_PENDIENTE', $selectedColumns)) {
                        if ($insc['IMPORTE_TOTAL_PENDIENTE'] > 0) {
                            $worksheet->write($row, $col, $insc['IMPORTE_TOTAL_PENDIENTE'], $childRed);
                        } else {
                            $worksheet->write($row, $col, $insc['IMPORTE_TOTAL_PENDIENTE'], $childGreen);
                        }
                        $col++;
                    }

                    if (in_array('IMPORTE_TOTAL_PENDIENTE_INSCRIPCION', $selectedColumns)) {
                        $worksheet->write($row, $col, '', $child);
                        $col++;
                    }

                    
                    if (in_array('first_payment_date', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['first_payment_date'], $child);
                        $col++;
                    }

                    
                    if (in_array('second_payment_date', $selectedColumns)) {
                        $worksheet->write($row, $col, $insc['second_payment_date'], $child);
                        $col++;
                    }

                    $row++;
                }
            }
        }
        
    }

    $workbook->close();
    $fh = fopen($fname, "rb");
    fpassthru($fh);
    unlink($fname);