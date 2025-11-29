# Instrucciones para subir a GitHub

## Paso 1: Crear repositorio en GitHub
1. Ve a https://github.com y inicia sesión
2. Click en el botón "+" → "New repository"
3. Nombre: `portafolio-ds4` (o el que prefieras)
4. Descripción: "Portafolio Desarrollo de Software IV"
5. Elige Public o Private
6. **NO marques** "Initialize with README"
7. Click en "Create repository"

## Paso 2: Conectar y subir

Después de crear el repositorio, ejecuta estos comandos (reemplaza TU_USUARIO con tu usuario de GitHub):

```bash
git remote add origin https://github.com/TU_USUARIO/portafolio-ds4.git
git branch -M main
git push -u origin main
```

## Si ya tienes el repositorio creado:

Si ya creaste el repositorio en GitHub, solo ejecuta:

```bash
git remote add origin https://github.com/TU_USUARIO/NOMBRE_DEL_REPO.git
git branch -M main
git push -u origin main
```

## Para futuros cambios:

Cuando hagas cambios y quieras subirlos:

```bash
git add .
git commit -m "Descripción de los cambios"
git push
```

## Habilitar GitHub Pages (para ver tu portafolio online):

1. Ve a tu repositorio en GitHub
2. Click en "Settings"
3. En el menú lateral, click en "Pages"
4. En "Source", selecciona "main" branch
5. Click en "Save"
6. Tu portafolio estará disponible en: `https://TU_USUARIO.github.io/portafolio-ds4/`

