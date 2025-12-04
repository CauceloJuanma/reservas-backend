## 🧩 Diagrama de Clases (Mermaid)

> Las operaciones se omiten por claridad.  
> Este diagrama muestra las entidades principales y sus relaciones.

```mermaid
classDiagram
        direction LR
    
        %% ==== DEFINICIÓN DE CLASES ====
    
        %% --- Usuarios ---
        class Usuario {
                String nombre
                String apellido
                String correo
                String pass
        }
        class TipoUsuario {
                String nombre
        }
    
        %% --- Pedidos ---
        class Reserva {
                Date fecha
        }
        class EstadoReserva {
                String nombre
        }
    
        %% --- Productos y Menús ---
        class Producto {
                String nombre
                String descripcion
                double precio
                int stock
        }
        class EstadoProducto {
                String nombre
        }
        class LineaProducto {
                int cantidad
                double precio_unitario
                double subtotal
        }
    
        %% --- Empresas ---
        class Empresa {
                String nombre
                String descripcion
                String direccion
                String telefono
                String email
        }
        class EstadoEmpresa {
                String nombre
        }
        class CargoUsuarioEmpresa {
                String nombre
        }
    
        %% ==== RELACIONES ====
    
        %% --- Usuario ---
        Usuario "*" -- "1" TipoUsuario : tipo
        Usuario "1" -- "*" Producto : quiere
        %% Si el Usuario incluye clientes y empleados, convendría que fuese opcional:
        Usuario "*" -- "0..1" Empresa : trabaja_en
        Usuario "*" -- "0..1" CargoUsuarioEmpresa : puesto

        %% --- Empresa ---
        Empresa "*" -- "1" EstadoEmpresa : estado
        Empresa "1" -- "*" Producto : oferta

        %% --- Producto ---
        Producto "1" -- "*" LineaProducto : contiene
        Producto "*" -- "1" EstadoProducto : tiene_historial
    
    
