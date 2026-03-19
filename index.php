<?php
$pdo = new PDO("mysql:host=localhost;dbname=test", "gothem", "");

$storages = $pdo
    ->query("SELECT id, nombre FROM bodegas")
    ->fetchAll(PDO::FETCH_ASSOC);

$currencies = $pdo
    ->query("SELECT id, nombre FROM monedas")
    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Document</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
        <h1>Formulario producto</h1>
        <form id="product_insert" name="product_insert" method="post" action="product_save.php" novalidate>
            <div class="form-grid">

                <div class="form-group">
                    <label for="code">Código</label>
                    <!-- Obligatorio, formato específico (letras, números), longitud mínima de 5 y máxima de 15 caracteres. -->
                    <input
                        type="text"
                        id="code"
                        name="code"
                        required
                        minlength="5"
                        maxlength="15"
                        pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$" />
                </div>

                <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" required minlength="2" maxlength="50"></input>
                </div>


                <div class="form-group">
                <label for="storage">Bodega</label>
                <select id="storage" name="storage" required>
                    <option value=""></option>
                    <?php foreach ($storages as $storage): ?>
                        <option value="<?= $storage["id"] ?>">
                            <?= $storage["nombre"] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                </div>

                <div class="form-group">
                <label for="store">Sucursal</label>
                <select id="store" name="store" required>
                    <option value=""></option>
                </select>
                </div>

                <div class="form-group">
                <label for="currency">Moneda</label>
                <select id="currency" name="currency" required>
                    <option value=""></option>
                    <?php foreach ($currencies as $currency): ?>
                        <option value="<?= $currency["id"] ?>">
                            <?= $currency["nombre"] ?>
                        </option>
                    <?php endforeach; ?>
                    <!-- Cargar opciones dinamicamente desde base de datos -->
                </select>
                </div>

                <div class="form-group">
                <label for="price">Precio</label>
                <!-- Obligatorio, formato de número positivo con hasta dos decimales. -->
                <input type="number" id="price" name="price" min="0" step="0.01" required ></input>
                </div>

                <div class="form-group full-width">
                <fieldset>
                    <legend>Material del Producto</legend>

                    <input type="checkbox" id="plastic" name="materials[]" value="plastico"></input>
                    <label for="plastic">Plástico</label>

                    <input type="checkbox" id="metal" name="materials[]" value="metal"></input>
                    <label for="metal">Metal</label>

                    <input type="checkbox" id="wood" name="materials[]" value="madera"></input>
                    <label for="wood">Madera</label>

                    <input type="checkbox" id="glass" name="materials[]" value="vidrio"></input>
                    <label for="glass">Vidrio</label>

                    <input type="checkbox" id="textile" name="materials[]" value="textil"></input>
                    <label for="textile">Textil</label>
                </fieldset>
                </div


>

                <div class="form-group full-width">
                <label for="description">Descripción</label>
                <!--  Obligatorio, longitud mínima de 10 caracteres y máxima de 1000 caracteres. -->
                <textarea id="description" name="description" minlength="10" maxlength="1000" required ></textarea>
                <button type="submit">Guardar Producto</button>
                </div>

            </div>
        </form>
        </div>
    </body>
</html>

<script>
// Cambio en el campo de bodegas
document.getElementById("storage").addEventListener("change",function(){
  let storageId = this.value;

  fetch("get_stores.php?storage_id=" + storageId)
    .then(response => response.json())
    .then(data => {
      let storeSelect = document.getElementById("store");
      storeSelect.innerHTML='';
      const empty_option = document.createElement('option');
      storeSelect.appendChild(empty_option);

      data.forEach(store => {
        const option = document.createElement('option');
        option.value = store.id;
        option.textContent = store.nombre;

        storeSelect.appendChild(option);
      });
    });
});

// Envio de forma
document.getElementById("product_insert").addEventListener("submit", async function(e) {
  e.preventDefault();

  const code = this.code;
  if(code.validity.valueMissing) {
    alert("El código del producto no puede estar en blanco.")
    return;
  } else if(code.validity.tooShort || code.validity.tooLong) {
    alert("El código del producto debe tener entre 5 y 15 caracteres.");
    return;
  } else if(code.validity.patternMismatch){
    alert("El código del producto debe contener letras y números");
    return;
  }

  const name = this.name;
  if(name.validity.valueMissing) {
    alert("El nombre del producto no puede estar en blanco.");
    return;
  } else if(name.validity.tooShort || name.validity.tooLong) {
    alert("El nombre del producto debe tener entre 2 y 50 caracteres.");
  }

  const price = this.price;
  if(price.validity.valueMissing) {
    alert("El precio del producto no puede estar en blanco.");
    return;
  } else if (price.validity.stepMismatch || price.validity.rangeUnderflow) {
    alert("El precio del producto debe ser un número positivo con hasta dos decimales.");
    return;
  }


  const checkboxes = document.querySelectorAll('input[name="materials[]"]:checked');
  if(checkboxes.length < 2) {
    alert("Debe seleccionar al menos dos materiales para el producto.");
    return;
  }

  const storage = this.storage;
  if(storage.validity.valueMissing) {
    alert("Debe seleccionar una bodega.");
    return;
  }

  const store = this.store;
  if(store.validity.valueMissing) {
    alert("Debe seleccionar una sucursal para la bodega seleccionada.");
    return;
  }

  const currency = this.currency;
  if(currency.validity.valueMissing) {
    alert("Debe seleccionar una moneda para el producto.");
    return;
  }

  const description = this.description;
  if(description.validity.valueMissing) {
    alert("La descripción del producto no puede estar en blanco.");
    return;
  } else if(description.validity.tooShort || description.validity.tooLong) {
    alert("La descripción del producto debe tener entre 10 y 1000 caracteres.");
    return;
  }

  const formData = new FormData(this);

  try {
    const response = await fetch("product_save.php", {
      method: "POST",
      body: formData,
    })

    const result = await response.text();
    if (result === "ok") {
      alert("Producto guardado correctamente");
    } else if (result === "duplicate") {
      alert("El código del producto ya está registrado.");
    } else {
      alert("Error al guardar el producto.");
    }
  } catch (error) {
    alert("Error en conexión.");
  }
  //this.submit();
});

</script>
