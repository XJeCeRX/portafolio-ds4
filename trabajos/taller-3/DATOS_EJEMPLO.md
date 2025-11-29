# 📊 Datos de Ejemplo Incluidos

## Productos de Ejemplo

El sistema incluye los siguientes productos pre-cargados en la base de datos:

1. **Café Expresso** - $4.00 (Stock: 100)
2. **Café Americano** - $3.00 (Stock: 150)
3. **Café Latte** - $4.50 (Stock: 80)
4. **Croissant** - $2.50 (Stock: 50)
5. **Torta de Chocolate** - $8.00 (Stock: 20)

## Proveedores de Ejemplo

1. **Café Premium Co.**
   - Contacto: Juan Pérez
   - Teléfono: 6000-1234
   - Email: juan@cafepremium.com

2. **Bebidas ABC**
   - Contacto: María García
   - Teléfono: 6000-5678
   - Email: maria@bebidasabc.com

## Cómo Usar el Sistema

### Proceso de Venta
1. Ir a **Ventas**
2. Seleccionar productos del panel derecho
3. Confirmar cliente y método de pago
4. Clic en "Procesar Venta"
5. El stock se actualiza automáticamente

### Agregar Productos
1. Ir a **Inventario**
2. Completar formulario en panel izquierdo
3. Seleccionar proveedor (opcional)
4. Guardar producto

### Registrar Proveedores
1. Ir a **Proveedores**
2. Completar información de contacto
3. Guardar proveedor

## Pruebas Recomendadas

### Flujo Completo de Venta
```
1. Agregar producto "Café Expresso" al carrito
2. Agregar producto "Croissant" al carrito
3. Confirmar venta
4. Verificar que el stock se actualizó
5. Verificar que la venta aparece en historial
```

### Control de Stock Bajo
```
1. Realizar varias ventas hasta que el stock < 10
2. Verificar que el producto aparece en ROJO en inventario
```

### Contacto
```
1. Enviar mensaje desde página principal
2. Verificar mensaje en tabla `contactos` de la BD
```

---

Estos datos permiten probar el sistema inmediatamente después de la instalación.


