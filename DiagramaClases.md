## Diagrama de Clases (Mermaid)

```mermaid
classDiagram
    class Usuario {
        +string nombre
        +string apellidos
        +string correo
        +string pass
    }

    class TipoUsuario {
        +string nombre
    }

    class Empresa {
        +string nombre
        +string descripcion
        +string direccion
        +string telefono
        +string email
    }

    class EstadoEmpresa {
        +string nombre
    }

    class CargoUsuarioEmpresa {
        +string nombre
    }

    class UsuarioEmpresa {
        %% idUsuario, idEmpresa, idCargo irían aquí como atributos si quieres
    }

    class Reserva {
        +double importe
        +datetime fecha_hora
    }

    class EstadoReserva {
        +string nombre
    }

    class Producto {
        +string nombre
        +string descripcion
        +double precio
        +int stock
        +time hora_ini
        +time hora_fin
    }

    class TipoProducto {
        +string nombre
    }

    class EstadoProducto {
        +string nombre
    }

    class LineaProducto {
        +int cantidad
        +double precio_unitario
        +double subtotal
    }

    %% Multiplicidades visibles

    %% TipoUsuario 1 -- * Usuario
    TipoUsuario "1" -- "*" Usuario : clasifica

    %% Usuario 1 -- * Reserva
    Usuario "1" -- "*" Reserva : realiza

    %% Usuario 1 -- * UsuarioEmpresa
    Usuario "1" -- "*" UsuarioEmpresa : tiene

    %% Empresa 1 -- * Reserva
    Empresa "1" -- "*" Reserva : recibe

    %% Empresa 1 -- * UsuarioEmpresa
    Empresa "1" -- "*" UsuarioEmpresa : agrupa

    %% Cargo 1 -- * UsuarioEmpresa
    CargoUsuarioEmpresa "1" -- "*" UsuarioEmpresa : define

    %% EstadoEmpresa 1 -- * Empresa
    EstadoEmpresa "1" -- "*" Empresa : clasifica

    %% EstadoReserva 1 -- * Reserva
    EstadoReserva "1" -- "*" Reserva : clasifica

    %% Empresa 1 -- * Producto
    Empresa "1" -- "*" Producto : ofrece

    %% TipoProducto 1 -- * Producto
    TipoProducto "1" -- "*" Producto : clasifica

    %% EstadoProducto 1 -- * Producto
    EstadoProducto "1" -- "*" Producto : clasifica

    %% Reserva 1 -- * LineaProducto
    Reserva "1" -- "*" LineaProducto : contiene

    %% Producto 1 -- * LineaProducto
    Producto "1" -- "*" LineaProducto : aparece_en
