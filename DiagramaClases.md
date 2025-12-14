## Diagrama de Clases (Mermaid)

```mermaid
erDiagram
    Usuario {
        string nombre
        string apellidos
        string correo
        string pass
    }

    TipoUsuario {
        string nombre
    }

    Empresa {
        string nombre
        string descripcion
        string direccion
        string telefono
        string email
    }

    EstadoEmpresa {
        string nombre
    }

    CargoUsuarioEmpresa {
        string nombre
    }

    UsuarioEmpresa {
    }

    Reserva {
        double importe
        datetime fecha_hora
    }

    EstadoReserva {
        string nombre
    }

    Producto {
        string nombre
        string descripcion
        double precio
        int stock
        time hora_ini
        time hora_fin
    }

    TipoProducto {
        string nombre
    }

    EstadoProducto {
        string nombre
    }

    LineaProducto {
        int cantidad
        double precio_unitario
        double subtotal
    }

    %% Relaciones (cardinalidades típicas)
    Usuario ||--o{ Reserva : realiza
    Usuario ||--o{ UsuarioEmpresa : tiene
    TipoUsuario ||--o{ Usuario : clasifica

    Empresa ||--o{ Reserva : recibe
    Empresa ||--o{ UsuarioEmpresa : agrupa
    Empresa ||--o{ Producto : ofrece
    EstadoEmpresa ||--o{ Empresa : clasifica

    CargoUsuarioEmpresa ||--o{ UsuarioEmpresa : define

    EstadoReserva ||--o{ Reserva : clasifica

    TipoProducto ||--o{ Producto : clasifica
    EstadoProducto ||--o{ Producto : clasifica

    Reserva ||--o{ LineaProducto : contiene
    Producto ||--o{ LineaProducto : aparece_en
