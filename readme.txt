git init 

git remote add origin https://github.com/ReyesPab/Proyecto-Implementacion-Grupo-6.git 

git checkout master
git pull origin master
git checkout -b nombre-rama

git add .

git commit -m "Commit inicial en nombre-de-tu-rama"

git push -u origin nombre-de-tu-rama



por si ya se creo la rama en la nube
git fetch origin
git checkout -b nombre-de-la-rama origin/nombre-de-la-rama

Actualizar de rama con los últimos cambios de master
git checkout master
git pull origin master
git checkout nombre-funcionalidad
git merge master