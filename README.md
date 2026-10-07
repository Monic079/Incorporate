# Incorpórate – Universidad Don Bosco

**🌐 Idioma / Language:** [Español](#espanol) · [English](#english)

---

<a id="espanol"></a>

<details open>
<summary><h2>🇪🇸 Español</h2></summary>

## Sistema Web para la Centralización y Automatización de la Gestión de Perfiles Profesionales y Oportunidades Laborales para el Programa "Incorpórate" de la Universidad Don Bosco

### Objetivo general

Agilizar la vinculación efectiva entre la comunidad estudiantil y el sector empresarial mediante un sistema web, facilitando una inserción laboral más rápida y organizada que potencie el crecimiento profesional de los participantes.

### Objetivos específicos

- Analizar el proceso actual de vinculación entre estudiantes y empresas para identificar las necesidades del sistema.
- Diseñar una plataforma web que permita a la empresa gestionar oportunidades de empleo o pasantía y a los estudiantes gestionar sus perfiles.
- Establecer un mecanismo de emparejamiento entre las skills y experiencias de los estudiantes y los requerimientos de las oportunidades.
- Implementar filtros que permitan a los reclutadores consultar y seleccionar perfiles compatibles con sus oportunidades.
- Realizar pruebas en cada plataforma y módulo.

### Funcionalidades por rol

| Rol | Funcionalidades |
|---|---|
| **Estudiantes / egresados** | Crear y consultar sus CVs (datos personales, carrera, skills, enlaces). Ver las oportunidades publicadas y postularse con el CV de su elección. |
| **Empresas** | Publicar, editar y eliminar oportunidades (trabajo o pasantía). Consultar el feed de talento con filtros (categoría, carrera, nivel, edad, skills y orden). Ver las postulaciones de cada oportunidad y cambiar su estado (aplicado, revisión, aceptado, rechazado). Ver los CVs con mayor coincidencia (matching). |
| **Administrador** | Gestionar el catálogo (carreras, skills y empresas). Crear usuarios, cambiar su rol y eliminarlos. Consultar CVs y oportunidades. |

### Matching: cómo se calcula la compatibilidad

Cada CV recibe un porcentaje de 0 a 100 respecto a una oportunidad. El cálculo se realiza en el momento en que la empresa abre el detalle de la oportunidad, y se muestran los 6 CVs con mayor porcentaje.

| Criterio | Peso | Regla |
|---|---|---|
| **Skills** | 50 | Proporcional: `50 × (skills del CV que pide la oportunidad ÷ skills que pide)`. Todas las skills valen lo mismo. |
| **Carrera** | 30 | Carrera exacta: 30 puntos. Misma categoría pero distinta carrera: 12 puntos (40%). Sin coincidencia: 0. |
| **Nivel** | 20 | Mismo nivel (estudiante/egresado): 20 puntos. Si además la oportunidad pide un año y el CV tiene otro: 15 puntos (75%). Nivel distinto: 0. |

```
porcentaje = round( puntos obtenidos ÷ puntos posibles × 100 )
```

Los **puntos posibles** solo incluyen los criterios que la oportunidad define. Por ejemplo, si no se define un nivel, el máximo es 80 y el porcentaje se calcula sobre esa base.

**Ejemplo:** oportunidad con Python, Docker e Inglés, Ingeniería en Software, nivel estudiante de 3.er año.

| CV | Cálculo | Resultado |
|---|---|---|
| Las 3 skills, Ing. en Software, estudiante 3.er año | 50 + 30 + 20 | **100%** |
| Solo Python, Ing. en Sistemas, estudiante 3.er año | 16.67 + 12 + 20 | **49%** |
| Todo igual al primero, pero de 2.º año | 50 + 30 + 15 | **95%** |
| Todo igual al primero, pero egresado | 50 + 30 + 0 | **80%** |

### Arquitectura

Tres aplicaciones web independientes, cada una con arquitectura **MVC** en PHP puro, que comparten una misma base de datos:

```
Incorporate/
├── admin/        # Aplicación del administrador
├── alumnos/      # Aplicación de estudiantes y egresados
├── empresas/     # Aplicación de empresas
├── database/
│   └── init.sql  # Esquema y datos iniciales
├── docker/       # Configuración de contenedores (en desarrollo)
├── k8s/          # Manifiestos de Kubernetes (en desarrollo)
└── docker-compose.yml
```

Cada aplicación contiene `config/`, `controllers/`, `models/`, `views/` y `assets/`.

**Tecnologías:** PHP 8.3, MySQL 8.4 (PDO), Apache 2.4, HTML, CSS y JavaScript. Autenticación con sesiones de PHP y contraseñas cifradas con `password_hash`. Control de acceso por rol (`admin`, `student`, `company`).

### Instalación (WampServer)

**Entorno probado:** WampServer 3.4.0 (64 bits), Apache 2.4.65, PHP 8.3.28, MySQL 8.4.7, phpMyAdmin 5.2.3.

1. Copiar la carpeta del proyecto en `C:\wamp64\www\` con el nombre exacto **`Incorporate`**.
2. Iniciar WampServer y esperar a que el ícono esté en verde.
3. Abrir phpMyAdmin (`http://localhost/phpmyadmin`), ir a **Importar** y seleccionar `database/init.sql`. Esto crea la base de datos `incorporate_1` con sus tablas y datos iniciales (carreras, skills, empresas y un usuario administrador).
4. Verificar la conexión en el archivo `config/database.php` de **cada** aplicación (`admin`, `alumnos` y `empresas`). Los valores por defecto son:

   | Parámetro | Valor |
   |---|---|
   | Servidor | `localhost` |
   | Base de datos | `incorporate_1` |
   | Usuario | `root` |
   | Contraseña | *(vacía)* |

   Si tu MySQL usa otros valores, deben modificarse en los tres archivos.

### Acceso

| Rol | URL |
|---|---|
| Empresas | `http://localhost/Incorporate/empresas/views/login_view.php` |
| Alumnos | `http://localhost/Incorporate/alumnos/views/login_view.php` |
| Administrador | `http://localhost/Incorporate/admin/views/login_view.php` |

Las cuentas de estudiante y empresa se crean desde el formulario de registro de cada aplicación. La cuenta de administrador inicial la crea `init.sql`.

### Docker y Kubernetes (en desarrollo)

El repositorio incluye una configuración inicial con Docker Compose (`docker-compose.yml`, `docker/`) y manifiestos de Kubernetes (`k8s/`). **Esta vía de despliegue está en desarrollo y aún no es el método de instalación soportado.** Para evaluar el sistema, utilizar WampServer según las instrucciones anteriores.

</details>

---

<a id="english"></a>

<details open>
<summary><h2>🇬🇧 English</h2></summary>

## Web System for the Centralization and Automation of Professional Profile and Job Opportunity Management for the "Incorpórate" Program at Universidad Don Bosco

### General objective

To streamline the effective link between the student community and the business sector through a web system, enabling a faster and better-organized job placement process that boosts the professional growth of participants.

### Specific objectives

- Analyze the current process of connecting students and companies in order to identify the system's needs.
- Design a web platform that allows companies to manage job and internship opportunities, and students to manage their profiles.
- Establish a matching mechanism between students' skills and experience and the requirements of each opportunity.
- Implement filters that allow recruiters to browse and select profiles compatible with their opportunities.
- Run tests on each platform and module.

### Features by role

| Role | Features |
|---|---|
| **Students / graduates** | Create and view their CVs (personal data, career, skills, links). Browse published opportunities and apply with the CV of their choice. |
| **Companies** | Publish, edit and delete opportunities (job or internship). Browse the talent feed with filters (category, career, level, age, skills and sorting). View the applications for each opportunity and change their status (applied, under review, accepted, rejected). View the best-matching CVs. |
| **Administrator** | Manage the catalog (careers, skills and companies). Create users, change their role and delete them. View CVs and opportunities. |

### Matching: how compatibility is calculated

Each CV receives a score from 0 to 100 against an opportunity. The score is calculated when the company opens the opportunity detail page, and the 6 highest-scoring CVs are shown.

| Criterion | Weight | Rule |
|---|---|---|
| **Skills** | 50 | Proportional: `50 × (CV skills required by the opportunity ÷ required skills)`. All skills weigh the same. |
| **Career** | 30 | Exact career: 30 points. Same category but different career: 12 points (40%). No match: 0. |
| **Level** | 20 | Same level (student/graduate): 20 points. If the opportunity also requires a year and the CV has a different one: 15 points (75%). Different level: 0. |

```
score = round( points earned ÷ possible points × 100 )
```

The **possible points** only include the criteria the opportunity defines. For example, if no level is defined, the maximum is 80 and the score is calculated on that basis.

**Example:** opportunity requiring Python, Docker and English, Software Engineering, 3rd-year student.

| CV | Calculation | Result |
|---|---|---|
| All 3 skills, Software Engineering, 3rd-year student | 50 + 30 + 20 | **100%** |
| Python only, Systems Engineering, 3rd-year student | 16.67 + 12 + 20 | **49%** |
| Same as the first, but 2nd year | 50 + 30 + 15 | **95%** |
| Same as the first, but graduate | 50 + 30 + 0 | **80%** |

### Architecture

Three independent web applications, each following the **MVC** pattern in plain PHP, sharing a single database:

```
Incorporate/
├── admin/        # Administrator application
├── alumnos/      # Students and graduates application
├── empresas/     # Companies application
├── database/
│   └── init.sql  # Schema and seed data
├── docker/       # Container configuration (in development)
├── k8s/          # Kubernetes manifests (in development)
└── docker-compose.yml
```

Each application contains `config/`, `controllers/`, `models/`, `views/` and `assets/`.

**Technologies:** PHP 8.3, MySQL 8.4 (PDO), Apache 2.4, HTML, CSS and JavaScript. Authentication uses PHP sessions and `password_hash`-encrypted passwords. Role-based access (`admin`, `student`, `company`).

### Installation (WampServer)

**Tested environment:** WampServer 3.4.0 (64-bit), Apache 2.4.65, PHP 8.3.28, MySQL 8.4.7, phpMyAdmin 5.2.3.

1. Copy the project folder into `C:\wamp64\www\` with the exact name **`Incorporate`**.
2. Start WampServer and wait until its icon turns green.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`), go to **Import** and select `database/init.sql`. This creates the `incorporate_1` database with its tables and seed data (careers, skills, companies and an administrator user).
4. Check the connection settings in the `config/database.php` file of **each** application (`admin`, `alumnos` and `empresas`). The defaults are:

   | Parameter | Value |
   |---|---|
   | Host | `localhost` |
   | Database | `incorporate_1` |
   | User | `root` |
   | Password | *(empty)* |

   If your MySQL uses different values, they must be changed in all three files.

### Access

| Role | URL |
|---|---|
| Companies | `http://localhost/Incorporate/empresas/views/login_view.php` |
| Students | `http://localhost/Incorporate/alumnos/views/login_view.php` |
| Administrator | `http://localhost/Incorporate/admin/views/login_view.php` |

Student and company accounts are created from each application's registration form. The initial administrator account is created by `init.sql`.

### Docker and Kubernetes (in development)

The repository includes an initial Docker Compose setup (`docker-compose.yml`, `docker/`) and Kubernetes manifests (`k8s/`). **This deployment path is in development and is not yet a supported installation method.** To evaluate the system, please use WampServer as described above.

</details>
