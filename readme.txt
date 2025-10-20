Para chequear el estado de la rama 
git checkout master
git pull origin master

Para crear una rama de trabajo 
git checkout -b nombre-de-su-rama

Para subir sus partes a sus ramas 
git add .
git commit -m "Agregado CRUD de productos"
git push -u origin ana-modulo-inventario



# Ver cambios
git status

# Agregar todos los cambios
git add .

# Hacer commit con mensaje claro
git commit -m "Agregado formulario de registro de usuarios"

# Subir tu rama al repositorio remoto (la primera vez)
git push -u origin nombre-de-tu-rama





# Cambiar a master
git checkout master

# Asegurarse de que esté actualizado
git pull origin master

# Fusionar tu rama
git merge nombre-de-tu-rama

# Subir a master
git push origin master
