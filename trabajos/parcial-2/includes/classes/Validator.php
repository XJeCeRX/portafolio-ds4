<?php
/**
 * Clase para validación de datos
 */
class Validator {
    
    /**
     * Validar datos de equipo
     */
    public static function validarEquipo($data, $esEdicion = false) {
        $errores = [];
        
        // Validar campos requeridos
        if (empty($data['equipo'])) {
            $errores[] = 'El nombre del equipo es requerido';
        } elseif (strlen($data['equipo']) > 100) {
            $errores[] = 'El nombre del equipo no puede exceder 100 caracteres';
        }
        
        if (empty($data['marca'])) {
            $errores[] = 'La marca es requerida';
        } elseif (strlen($data['marca']) > 50) {
            $errores[] = 'La marca no puede exceder 50 caracteres';
        }
        
        if (empty($data['serie'])) {
            $errores[] = 'El número de serie es requerido';
        } elseif (strlen($data['serie']) > 50) {
            $errores[] = 'El número de serie no puede exceder 50 caracteres';
        }
        
        if (empty($data['fecha_ingreso'])) {
            $errores[] = 'La fecha de ingreso es requerida';
        } else {
            // Validar formato de fecha
            $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_ingreso']);
            if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_ingreso']) {
                $errores[] = 'La fecha de ingreso no es válida';
            } else {
                // La fecha no puede ser futura
                if ($fecha > new DateTime()) {
                    $errores[] = 'La fecha de ingreso no puede ser futura';
                }
            }
        }
        
        // Validar fecha de salida
        if (!empty($data['fecha_salida'])) {
            $fechaSalida = DateTime::createFromFormat('Y-m-d', $data['fecha_salida']);
            if (!$fechaSalida || $fechaSalida->format('Y-m-d') !== $data['fecha_salida']) {
                $errores[] = 'La fecha de salida no es válida';
            } else {
                // Fecha de salida no puede ser anterior a fecha de ingreso
                if (!empty($data['fecha_ingreso'])) {
                    $fechaIngreso = DateTime::createFromFormat('Y-m-d', $data['fecha_ingreso']);
                    if ($fechaSalida < $fechaIngreso) {
                        $errores[] = 'La fecha de salida no puede ser anterior a la fecha de ingreso';
                    }
                }
            }
        }
        
        // Validar tipo de servicio
        $tiposValidos = ['mantenimiento', 'reparacion', 'calibracion', 'revision'];
        if (!empty($data['tipo_servicio']) && !in_array($data['tipo_servicio'], $tiposValidos)) {
            $errores[] = 'Tipo de servicio no válido';
        }
        
        // Validar costos
        if (isset($data['costo_inicial'])) {
            $costoInicial = floatval($data['costo_inicial']);
            if ($costoInicial < 0) {
                $errores[] = 'El costo inicial no puede ser negativo';
            }
            if ($costoInicial > 9999999.99) {
                $errores[] = 'El costo inicial excede el límite permitido';
            }
        }
        
        if (isset($data['costo_final'])) {
            $costoFinal = floatval($data['costo_final']);
            if ($costoFinal < 0) {
                $errores[] = 'El costo final no puede ser negativo';
            }
            if ($costoFinal > 9999999.99) {
                $errores[] = 'El costo final excede el límite permitido';
            }
        }
        
        // Validar estado
        $estadosValidos = ['ingresado', 'en_proceso', 'completado', 'entregado'];
        if (!empty($data['estado']) && !in_array($data['estado'], $estadosValidos)) {
            $errores[] = 'Estado no válido';
        }
        
        // Validar observación (opcional pero con límite)
        if (!empty($data['observacion']) && strlen($data['observacion']) > 1000) {
            $errores[] = 'La observación no puede exceder 1000 caracteres';
        }
        
        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }
    
    /**
     * Validar datos de mantenimiento
     */
    public static function validarMantenimiento($data, $esEdicion = false) {
        $errores = [];
        
        // Validar equipo
        if (empty($data['id_equipo'])) {
            $errores[] = 'El equipo es requerido';
        } else {
            // Validar que el equipo exista
            $equipo = new Equipo();
            $equipoData = $equipo->obtenerPorId($data['id_equipo']);
            if (!$equipoData) {
                $errores[] = 'El equipo seleccionado no existe';
            }
        }
        
        // Validar tipo de mantenimiento
        $tiposValidos = ['predictivo', 'preventivo', 'correctivo'];
        if (empty($data['tipo_mantenimiento'])) {
            $errores[] = 'El tipo de mantenimiento es requerido';
        } elseif (!in_array($data['tipo_mantenimiento'], $tiposValidos)) {
            $errores[] = 'Tipo de mantenimiento no válido';
        }
        
        // Validar estado
        $estadosValidos = ['por_hacer', 'en_espera_material', 'en_revision', 'terminada'];
        if (!empty($data['estado']) && !in_array($data['estado'], $estadosValidos)) {
            $errores[] = 'Estado no válido';
        }
        
        // Validar porcentaje de avance
        if (isset($data['porcentaje_avance'])) {
            $porcentaje = intval($data['porcentaje_avance']);
            if ($porcentaje < 0 || $porcentaje > 100) {
                $errores[] = 'El porcentaje de avance debe estar entre 0 y 100';
            }
        }
        
        // Validar fechas
        if (!empty($data['fecha_inicio'])) {
            $fechaInicio = DateTime::createFromFormat('Y-m-d', $data['fecha_inicio']);
            if (!$fechaInicio || $fechaInicio->format('Y-m-d') !== $data['fecha_inicio']) {
                $errores[] = 'La fecha de inicio no es válida';
            }
        }
        
        if (!empty($data['fecha_fin_prevista'])) {
            $fechaFin = DateTime::createFromFormat('Y-m-d', $data['fecha_fin_prevista']);
            if (!$fechaFin || $fechaFin->format('Y-m-d') !== $data['fecha_fin_prevista']) {
                $errores[] = 'La fecha fin prevista no es válida';
            } else {
                // Fecha fin no puede ser anterior a fecha inicio
                if (!empty($data['fecha_inicio'])) {
                    $fechaInicio = DateTime::createFromFormat('Y-m-d', $data['fecha_inicio']);
                    if ($fechaFin < $fechaInicio) {
                        $errores[] = 'La fecha fin prevista no puede ser anterior a la fecha de inicio';
                    }
                }
            }
        }
        
        // Validar costo
        if (isset($data['costo_mantenimiento'])) {
            $costo = floatval($data['costo_mantenimiento']);
            if ($costo < 0) {
                $errores[] = 'El costo de mantenimiento no puede ser negativo';
            }
            if ($costo > 9999999.99) {
                $errores[] = 'El costo de mantenimiento excede el límite permitido';
            }
        }
        
        // Validar descripción
        if (!empty($data['descripcion']) && strlen($data['descripcion']) > 500) {
            $errores[] = 'La descripción no puede exceder 500 caracteres';
        }
        
        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }
    
    /**
     * Validar ID numérico
     */
    public static function validarId($id) {
        return is_numeric($id) && $id > 0 && $id == intval($id);
    }
    
    /**
     * Sanitizar string
     */
    public static function sanitizeString($string, $maxLength = null) {
        $string = trim($string);
        $string = stripslashes($string);
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        
        if ($maxLength !== null && strlen($string) > $maxLength) {
            $string = substr($string, 0, $maxLength);
        }
        
        return $string;
    }
    
    /**
     * Validar email
     */
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}



