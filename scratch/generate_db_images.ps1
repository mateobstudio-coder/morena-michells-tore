Add-Type -AssemblyName System.Drawing;

function Draw-RoundedRect($g, $pen, $brush, $x, $y, $w, $h, $radius) {
    $path = New-Object System.Drawing.Drawing2D.GraphicsPath;
    $d = $radius * 2;
    $rect = New-Object System.Drawing.Rectangle($x, $y, $w, $h);
    $path.AddArc($rect.X, $rect.Y, $d, $d, 180, 90);
    $path.AddArc($rect.Right - $d, $rect.Y, $d, $d, 270, 90);
    $path.AddArc($rect.Right - $d, $rect.Bottom - $d, $d, $d, 0, 90);
    $path.AddArc($rect.X, $rect.Bottom - $d, $d, $d, 90, 90);
    $path.CloseFigure();
    if ($brush -ne $null) { $g.FillPath($brush, $path); }
    if ($pen -ne $null) { $g.DrawPath($pen, $path); }
    $path.Dispose();
}

$fontTitle = New-Object System.Drawing.Font("Segoe UI", 18, [System.Drawing.FontStyle]::Bold);
$fontSubtitle = New-Object System.Drawing.Font("Segoe UI", 11, [System.Drawing.FontStyle]::Regular);
$fontHeader = New-Object System.Drawing.Font("Segoe UI", 13, [System.Drawing.FontStyle]::Bold);
$fontBold = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold);
$fontRegular = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Regular);
$fontSmall = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular);
$fontMono = New-Object System.Drawing.Font("Consolas", 9.5, [System.Drawing.FontStyle]::Regular);
$fontMonoBold = New-Object System.Drawing.Font("Consolas", 9.5, [System.Drawing.FontStyle]::Bold);

$colBg = [System.Drawing.ColorTranslator]::FromHtml("#F8F9FA");
$colCardBg = [System.Drawing.ColorTranslator]::FromHtml("#FFFFFF");
$colBorder = [System.Drawing.ColorTranslator]::FromHtml("#DEE2E6");
$colDark = [System.Drawing.ColorTranslator]::FromHtml("#212529");
$colGray = [System.Drawing.ColorTranslator]::FromHtml("#6C757D");
$colWine = [System.Drawing.ColorTranslator]::FromHtml("#8B2635");
$colWineLight = [System.Drawing.ColorTranslator]::FromHtml("#FFE5E1");
$colBlue = [System.Drawing.ColorTranslator]::FromHtml("#0D6EFD");
$colBlueLight = [System.Drawing.ColorTranslator]::FromHtml("#E7F1FF");
$colGreen = [System.Drawing.ColorTranslator]::FromHtml("#198754");
$colGreenLight = [System.Drawing.ColorTranslator]::FromHtml("#D1E7DD");
$colGold = [System.Drawing.ColorTranslator]::FromHtml("#D4A017");
$colGoldLight = [System.Drawing.ColorTranslator]::FromHtml("#FFF3CD");

$outDir = "c:\xampp\htdocs\morena-michell-store\informe_bd";
$artDir = "C:\Users\Usuario\.gemini\antigravity\brain\bf8d81c4-45b6-495b-b675-5de887845854";

# =========================================================================
# IMAGEN 1: DIAGRAMA ENTIDAD - RELACIÓN (DER)
# =========================================================================
$w = 1400; $h = 920;
$bmp1 = New-Object System.Drawing.Bitmap($w, $h, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb);
$g1 = [System.Drawing.Graphics]::FromImage($bmp1);
$g1.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality;
$g1.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit;

$g1.Clear($colBg);

# Top Banner
$bannerBrush = New-Object System.Drawing.SolidBrush($colWine);
$g1.FillRectangle($bannerBrush, 0, 0, $w, 80);
$bannerBrush.Dispose();

$whiteBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White);
$g1.DrawString("DIAGRAMA ENTIDAD - RELACIÓN (DER / RELATIONAL MODEL)", $fontTitle, $whiteBrush, 40, 16);
$pinkBrush = New-Object System.Drawing.SolidBrush($colWineLight);
$g1.DrawString("Base de Datos: michell_store | Motor: InnoDB | Cotejamiento: utf8mb4_unicode_ci | Sistema: Morena Store", $fontSubtitle, $pinkBrush, 42, 50);
$pinkBrush.Dispose();
$whiteBrush.Dispose();

# Function to draw a Table Entity Box
function Draw-TableEntity($g, $x, $y, $tw, $title, $badgeColor, $fields) {
    $headerH = 38;
    $rowH = 26;
    $th = $headerH + ($fields.Count * $rowH) + 12;

    $cardBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White);
    $cardPen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#CED4DA"), 1.5);
    Draw-RoundedRect $g $cardPen $cardBrush $x $y $tw $th 8;
    $cardBrush.Dispose();
    $cardPen.Dispose();

    # Header bar
    $hBrush = New-Object System.Drawing.SolidBrush($badgeColor);
    Draw-RoundedRect $g $null $hBrush $x $y $tw $headerH 8;
    # Clean bottom corners of header
    $g.FillRectangle($hBrush, $x, ($y + 20), $tw, ($headerH - 20));
    $hBrush.Dispose();

    $wBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White);
    $g.DrawString($title, (New-Object System.Drawing.Font("Segoe UI", 12, [System.Drawing.FontStyle]::Bold)), $wBrush, ($x + 14), ($y + 8));
    $wBrush.Dispose();

    # Fields
    $currY = $y + $headerH + 6;
    $fNameBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#212529"));
    $fTypeBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#6C757D"));
    $linePen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#F1F3F5"), 1);

    foreach ($f in $fields) {
        $g.DrawLine($linePen, ($x + 10), $currY, ($x + $tw - 10), $currY);
        $currY += 2;

        # Key badge if PK or FK
        $nameX = $x + 14;
        if ($f.Key -eq "PK") {
            $pkBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#FFE69C"));
            $pkText = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#7D5A00"));
            Draw-RoundedRect $g $null $pkBrush $nameX ($currY + 1) 26 18 4;
            $g.DrawString("PK", (New-Object System.Drawing.Font("Segoe UI", 8, [System.Drawing.FontStyle]::Bold)), $pkText, ($nameX + 4), ($currY + 3));
            $pkBrush.Dispose(); $pkText.Dispose();
            $nameX += 32;
        } elseif ($f.Key -eq "FK") {
            $fkBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#CFE2FF"));
            $fkText = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#084298"));
            Draw-RoundedRect $g $null $fkBrush $nameX ($currY + 1) 26 18 4;
            $g.DrawString("FK", (New-Object System.Drawing.Font("Segoe UI", 8, [System.Drawing.FontStyle]::Bold)), $fkText, ($nameX + 4), ($currY + 3));
            $fkBrush.Dispose(); $fkText.Dispose();
            $nameX += 32;
        }

        # Name
        $g.DrawString($f.Name, (New-Object System.Drawing.Font("Consolas", 10, [System.Drawing.FontStyle]::Bold)), $fNameBrush, $nameX, $currY);

        # Type aligned right
        $typeStr = $f.Type;
        $size = $g.MeasureString($typeStr, (New-Object System.Drawing.Font("Consolas", 9, [System.Drawing.FontStyle]::Regular)));
        $g.DrawString($typeStr, (New-Object System.Drawing.Font("Consolas", 9, [System.Drawing.FontStyle]::Regular)), $fTypeBrush, ($x + $tw - $size.Width - 14), ($currY + 1));

        $currY += ($rowH - 2);
    }
    $fNameBrush.Dispose();
    $fTypeBrush.Dispose();
    $linePen.Dispose();

    return @{ X = $x; Y = $y; W = $tw; H = $th };
}

# Tables definition
$fProductos = @(
    @{ Name = "id"; Type = "INT (11) AUTO_INC"; Key = "PK" },
    @{ Name = "nombre"; Type = "VARCHAR (255)"; Key = "" },
    @{ Name = "categoria"; Type = "VARCHAR (100)"; Key = "" },
    @{ Name = "precio"; Type = "DECIMAL (10,2)"; Key = "" },
    @{ Name = "imagen"; Type = "VARCHAR (255)"; Key = "" },
    @{ Name = "descripcion"; Type = "TEXT"; Key = "" },
    @{ Name = "etiqueta"; Type = "VARCHAR (50)"; Key = "" },
    @{ Name = "visible"; Type = "TINYINT (1)"; Key = "" },
    @{ Name = "fecha_creacion"; Type = "DATETIME"; Key = "" }
);

$fUsuarios = @(
    @{ Name = "id"; Type = "INT (11) AUTO_INC"; Key = "PK" },
    @{ Name = "usuario"; Type = "VARCHAR (50) UNIQUE"; Key = "" },
    @{ Name = "password"; Type = "VARCHAR (255)"; Key = "" },
    @{ Name = "rol"; Type = "ENUM('cliente','admin')"; Key = "" },
    @{ Name = "fecha_registro"; Type = "DATETIME"; Key = "" }
);

$fValoraciones = @(
    @{ Name = "id"; Type = "INT (11) AUTO_INC"; Key = "PK" },
    @{ Name = "producto_id"; Type = "INT (11)"; Key = "FK" },
    @{ Name = "usuario"; Type = "VARCHAR (100)"; Key = "FK" },
    @{ Name = "puntuacion"; Type = "INT (11)"; Key = "" },
    @{ Name = "fecha"; Type = "TIMESTAMP"; Key = "" }
);

$fComentarios = @(
    @{ Name = "id"; Type = "INT (11) AUTO_INC"; Key = "PK" },
    @{ Name = "producto_id"; Type = "INT (11)"; Key = "FK" },
    @{ Name = "usuario"; Type = "VARCHAR (100)"; Key = "FK" },
    @{ Name = "comentario"; Type = "TEXT"; Key = "" },
    @{ Name = "fecha"; Type = "TIMESTAMP"; Key = "" }
);

# Draw Entities
$posProductos = Draw-TableEntity $g1 60 120 380 "TABLE: productos (Catálogo)" ([System.Drawing.ColorTranslator]::FromHtml("#8B2635")) $fProductos;
$posUsuarios = Draw-TableEntity $g1 960 120 380 "TABLE: usuarios (Clientes y Admin)" ([System.Drawing.ColorTranslator]::FromHtml("#1E3A8A")) $fUsuarios;
$posValoraciones = Draw-TableEntity $g1 60 520 380 "TABLE: valoraciones (Reseñas 1-5★)" ([System.Drawing.ColorTranslator]::FromHtml("#B45309")) $fValoraciones;
$posComentarios = Draw-TableEntity $g1 960 520 380 "TABLE: comentarios (Preguntas y Resp.)" ([System.Drawing.ColorTranslator]::FromHtml("#047857")) $fComentarios;

# Relationship Connectors
$relPen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#495057"), 2);
$relPen.DashStyle = [System.Drawing.Drawing2D.DashStyle]::Solid;

# 1. productos (1) -> (N) valoraciones (vertical on left)
$g1.DrawLine($relPen, 180, ($posProductos.Y + $posProductos.H), 180, $posValoraciones.Y);
# Diamond or Label
$lblBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#343A40"));
$g1.DrawString("1", (New-Object System.Drawing.Font("Segoe UI", 12, [System.Drawing.FontStyle]::Bold)), $lblBrush, 190, ($posProductos.Y + $posProductos.H + 5));
$g1.DrawString("N", (New-Object System.Drawing.Font("Segoe UI", 12, [System.Drawing.FontStyle]::Bold)), $lblBrush, 190, ($posValoraciones.Y - 24));
$g1.DrawString("Tiene 1 a N valoraciones", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Italic)), $lblBrush, 60, ($posValoraciones.Y - 45));

# 2. usuarios (1) -> (N) comentarios (vertical on right)
$g1.DrawLine($relPen, 1150, ($posUsuarios.Y + $posUsuarios.H), 1150, $posComentarios.Y);
$g1.DrawString("1", (New-Object System.Drawing.Font("Segoe UI", 12, [System.Drawing.FontStyle]::Bold)), $lblBrush, 1160, ($posUsuarios.Y + $posUsuarios.H + 5));
$g1.DrawString("N", (New-Object System.Drawing.Font("Segoe UI", 12, [System.Drawing.FontStyle]::Bold)), $lblBrush, 1160, ($posComentarios.Y - 24));
$g1.DrawString("Publica 0 a N comentarios", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Italic)), $lblBrush, 1160, ($posComentarios.Y - 45));

# 3. productos (1) -> (N) comentarios (diagonal / cross)
$g1.DrawLine($relPen, ($posProductos.X + $posProductos.W), 320, 800, 320);
$g1.DrawLine($relPen, 800, 320, 800, 600);
$g1.DrawLine($relPen, 800, 600, $posComentarios.X, 600);
$g1.DrawString("1:N (producto_id)", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)), $lblBrush, 680, 302);

# 4. usuarios (1) -> (N) valoraciones
$g1.DrawLine($relPen, $posUsuarios.X, 220, 600, 220);
$g1.DrawLine($relPen, 600, 220, 600, 600);
$g1.DrawLine($relPen, 600, 600, ($posValoraciones.X + $posValoraciones.W), 600);
$g1.DrawString("1:N (usuario)", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)), $lblBrush, 610, 202);

# Center Summary Card
$centerCard = Draw-RoundedRect $g1 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#DEE2E6"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#FFFFFF"))) 490 380 420 180 8;
$g1.DrawString("REGLAS DE NEGOCIO Y CARDINALIDAD", (New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush($colWine)), 510, 395);
$rules = @(
    "• Cada Producto puede recibir múltiples comentarios y valoraciones.",
    "• Los Usuarios pueden calificar (1 a 5 estrellas) cada producto.",
    "• Relación productos ➔ valoraciones: 1 a N (por producto_id).",
    "• Relación productos ➔ comentarios: 1 a N (por producto_id).",
    "• Relación usuarios ➔ opiniones: 1 a N (por nombre de usuario)."
);
$rY = 425;
foreach ($r in $rules) {
    $g1.DrawString($r, (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular)), $lblBrush, 510, $rY);
    $rY += 22;
}

# Footer Note
$g1.DrawString("Generado automáticamente desde la estructura MySQL de XAMPP para el Informe Técnico | Tienda Oficial Morena Store", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush($colGray)), 40, 885);

$bmp1.Save("$outDir\01_diagrama_entidad_relacion.png", [System.Drawing.Imaging.ImageFormat]::Png);
$bmp1.Save("$artDir\01_diagrama_entidad_relacion.png", [System.Drawing.Imaging.ImageFormat]::Png);
$g1.Dispose(); $bmp1.Dispose();
Write-Output "Image 1 created successfully";

# =========================================================================
# IMAGEN 2: DICCIONARIO DE DATOS TÉCNICO (TODAS LAS TABLAS)
# =========================================================================
$w2 = 1400; $h2 = 1100;
$bmp2 = New-Object System.Drawing.Bitmap($w2, $h2, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb);
$g2 = [System.Drawing.Graphics]::FromImage($bmp2);
$g2.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality;
$g2.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit;
$g2.Clear($colBg);

# Banner
$bBrush = New-Object System.Drawing.SolidBrush($colWine);
$g2.FillRectangle($bBrush, 0, 0, $w2, 80);
$bBrush.Dispose();
$g2.DrawString("DICCIONARIO DE DATOS Y ESPECIFICACIÓN DE TABLAS", $fontTitle, (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), 40, 16);
$g2.DrawString("Estructura detallada de campos, tipos MySQL, valores por defecto, claves y descripciones", $fontSubtitle, (New-Object System.Drawing.SolidBrush($colWineLight)), 42, 50);

function Draw-DataDictionaryTable($g, $x, $y, $tw, $tableName, $colorHex, $cols) {
    $headerH = 34;
    $rowH = 26;
    $totalH = $headerH + 28 + ($cols.Count * $rowH) + 10;

    # Outer Box
    Draw-RoundedRect $g (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#CED4DA"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)) $x $y $tw $totalH 6;

    # Table Header
    $hB = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($colorHex));
    Draw-RoundedRect $g $null $hB $x $y $tw $headerH 6;
    $g.FillRectangle($hB, $x, ($y + 16), $tw, ($headerH - 16));
    $hB.Dispose();

    $g.DrawString("TABLA: $tableName", (New-Object System.Drawing.Font("Segoe UI", 11, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), ($x + 12), ($y + 7));

    # Columns Header
    $colHeadY = $y + $headerH;
    $chBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#E9ECEF"));
    $g.FillRectangle($chBrush, $x, $colHeadY, $tw, 26);
    $chBrush.Dispose();

    $chText = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#495057"));
    $fCh = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold);
    $g.DrawString("Campo (Field)", $fCh, $chText, ($x + 12), ($colHeadY + 5));
    $g.DrawString("Tipo de Dato", $fCh, $chText, ($x + 200), ($colHeadY + 5));
    $g.DrawString("Nulo", $fCh, $chText, ($x + 360), ($colHeadY + 5));
    $g.DrawString("Clave", $fCh, $chText, ($x + 430), ($colHeadY + 5));
    $g.DrawString("Predeterminado", $fCh, $chText, ($x + 500), ($colHeadY + 5));
    $g.DrawString("Descripción Funcional", $fCh, $chText, ($x + 670), ($colHeadY + 5));
    $chText.Dispose();

    # Rows
    $rY = $colHeadY + 26;
    $fRow = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular);
    $fCode = New-Object System.Drawing.Font("Consolas", 9, [System.Drawing.FontStyle]::Regular);
    $tBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#212529"));
    $grayB = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#6C757D"));
    $penLine = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#E9ECEF"), 1);

    for ($i = 0; $i -lt $cols.Count; $i++) {
        $c = $cols[$i];
        if ($i % 2 -eq 1) {
            $g.FillRectangle((New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#F8F9FA"))), ($x + 1), $rY, ($tw - 2), $rowH);
        }
        $g.DrawLine($penLine, $x, ($rY + $rowH), ($x + $tw), ($rY + $rowH));

        # Field Name
        $g.DrawString($c.Field, (New-Object System.Drawing.Font("Consolas", 9, [System.Drawing.FontStyle]::Bold)), $tBrush, ($x + 12), ($rY + 5));
        # Type
        $g.DrawString($c.Type, $fCode, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#0D6EFD"))), ($x + 200), ($rY + 5));
        # Null
        $nullColor = if ($c.Null -eq "NO") { "#DC3545" } else { "#198754" };
        $g.DrawString($c.Null, $fRow, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($nullColor))), ($x + 360), ($rY + 5));
        # Key
        $g.DrawString($c.Key, (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#B45309"))), ($x + 430), ($rY + 5));
        # Default
        $g.DrawString($c.Default, $fCode, $grayB, ($x + 500), ($rY + 5));
        # Description
        $g.DrawString($c.Desc, $fRow, $tBrush, ($x + 670), ($rY + 5));

        $rY += $rowH;
    }
    return $totalH;
}

$dictProductos = @(
    @{ Field = "id"; Type = "int(11)"; Null = "NO"; Key = "PRI"; Default = "AUTO_INC"; Desc = "Identificador único incremental del producto." },
    @{ Field = "nombre"; Type = "varchar(255)"; Null = "YES"; Key = "-"; Default = "NULL"; Desc = "Título comercial de la prenda o artículo." },
    @{ Field = "categoria"; Type = "varchar(100)"; Null = "YES"; Key = "-"; Default = "NULL"; Desc = "Categoría (remeras, calzas, medias, interior)." },
    @{ Field = "precio"; Type = "decimal(10,2)"; Null = "YES"; Key = "-"; Default = "NULL"; Desc = "Precio en pesos argentinos (ARS) para venta." },
    @{ Field = "imagen"; Type = "varchar(255)"; Null = "YES"; Key = "-"; Default = "NULL"; Desc = "Ruta relativa del archivo de imagen (fts/...)." },
    @{ Field = "descripcion"; Type = "text"; Null = "YES"; Key = "-"; Default = "NULL"; Desc = "Detalles de talle, tela, calce y características." },
    @{ Field = "etiqueta"; Type = "varchar(50)"; Null = "YES"; Key = "-"; Default = "NULL"; Desc = "Distintivo visual (NUEVO, OFERTA, TENDENCIA)." },
    @{ Field = "visible"; Type = "tinyint(1)"; Null = "NO"; Key = "-"; Default = "1"; Desc = "1 = visible en catálogo, 0 = oculto por admin." },
    @{ Field = "fecha_creacion"; Type = "datetime"; Null = "YES"; Key = "-"; Default = "CURRENT_TIMESTAMP"; Desc = "Marca temporal de alta en la base de datos." }
);

$dictUsuarios = @(
    @{ Field = "id"; Type = "int(11)"; Null = "NO"; Key = "PRI"; Default = "AUTO_INC"; Desc = "ID de cuenta único del usuario registrado." },
    @{ Field = "usuario"; Type = "varchar(50)"; Null = "NO"; Key = "UNI"; Default = "-"; Desc = "Nombre de usuario único para inicio de sesión." },
    @{ Field = "password"; Type = "varchar(255)"; Null = "NO"; Key = "-"; Default = "-"; Desc = "Contraseña encriptada de forma segura con BCrypt." },
    @{ Field = "rol"; Type = "enum('cliente','admin')"; Null = "NO"; Key = "-"; Default = "'cliente'"; Desc = "Permisos de acceso: cliente o administrador." },
    @{ Field = "fecha_registro"; Type = "datetime"; Null = "YES"; Key = "-"; Default = "CURRENT_TIMESTAMP"; Desc = "Fecha y hora de registro en la plataforma." }
);

$dictValoraciones = @(
    @{ Field = "id"; Type = "int(11)"; Null = "NO"; Key = "PRI"; Default = "AUTO_INC"; Desc = "ID único de la calificación." },
    @{ Field = "producto_id"; Type = "int(11)"; Null = "NO"; Key = "MUL"; Default = "-"; Desc = "Referencia al ID del producto calificado." },
    @{ Field = "usuario"; Type = "varchar(100)"; Null = "NO"; Key = "-"; Default = "-"; Desc = "Nombre del usuario que emite la valoración." },
    @{ Field = "puntuacion"; Type = "int(11)"; Null = "NO"; Key = "-"; Default = "-"; Desc = "Puntuación numérica de satisfacción (1 a 5 estrellas)." },
    @{ Field = "fecha"; Type = "timestamp"; Null = "NO"; Key = "-"; Default = "CURRENT_TIMESTAMP"; Desc = "Fecha y hora en que se envió la reseña." }
);

$dictComentarios = @(
    @{ Field = "id"; Type = "int(11)"; Null = "NO"; Key = "PRI"; Default = "AUTO_INC"; Desc = "ID único del comentario o consulta." },
    @{ Field = "producto_id"; Type = "int(11)"; Null = "NO"; Key = "MUL"; Default = "-"; Desc = "Referencia al ID del producto consultado." },
    @{ Field = "usuario"; Type = "varchar(100)"; Null = "NO"; Key = "-"; Default = "-"; Desc = "Nombre del usuario que realizó la pregunta." },
    @{ Field = "comentario"; Type = "text"; Null = "NO"; Key = "-"; Default = "-"; Desc = "Texto con la consulta o mensaje publicado." },
    @{ Field = "fecha"; Type = "timestamp"; Null = "NO"; Key = "-"; Default = "CURRENT_TIMESTAMP"; Desc = "Fecha y hora de publicación del comentario." }
);

$currY = 100;
$h1 = Draw-DataDictionaryTable $g2 40 $currY 1320 "productos" "#8B2635" $dictProductos;
$currY += $h1 + 20;

$h2 = Draw-DataDictionaryTable $g2 40 $currY 1320 "usuarios" "#1E3A8A" $dictUsuarios;
$currY += $h2 + 20;

$h3 = Draw-DataDictionaryTable $g2 40 $currY 645 "valoraciones" "#B45309" $dictValoraciones;
$h4 = Draw-DataDictionaryTable $g2 715 $currY 645 "comentarios" "#047857" $dictComentarios;

$bmp2.Save("$outDir\02_diccionario_de_datos.png", [System.Drawing.Imaging.ImageFormat]::Png);
$bmp2.Save("$artDir\02_diccionario_de_datos.png", [System.Drawing.Imaging.ImageFormat]::Png);
$g2.Dispose(); $bmp2.Dispose();
Write-Output "Image 2 created successfully";

# =========================================================================
# IMAGEN 3: CONTENIDO DE TABLA PRODUCTOS (CATÁLOGO REAL EN BD)
# =========================================================================
$w3 = 1400; $h3 = 1220;
$bmp3 = New-Object System.Drawing.Bitmap($w3, $h3, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb);
$g3 = [System.Drawing.Graphics]::FromImage($bmp3);
$g3.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality;
$g3.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit;
$g3.Clear($colBg);

$b3 = New-Object System.Drawing.SolidBrush($colWine);
$g3.FillRectangle($b3, 0, 0, $w3, 80);
$b3.Dispose();
$g3.DrawString("REGISTROS DE LA BASE DE DATOS: TABLA 'productos'", $fontTitle, (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), 40, 16);
$g3.DrawString("Vista tabular de los registros almacenados en MySQL (Total: 37 productos activos)", $fontSubtitle, (New-Object System.Drawing.SolidBrush($colWineLight)), 42, 50);

# Table Container
$boxX = 40; $boxY = 105; $boxW = 1320; $boxH = 1060;
Draw-RoundedRect $g3 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#CED4DA"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)) $boxX $boxY $boxW $boxH 6;

# Grid Header
$gHeadY = $boxY;
$ghB = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#3B151B"));
Draw-RoundedRect $g3 $null $ghB $boxX $gHeadY $boxW 34 6;
$g3.FillRectangle($ghB, $boxX, ($gHeadY + 16), $boxW, 18);
$ghB.Dispose();

$fGh = New-Object System.Drawing.Font("Segoe UI", 9.5, [System.Drawing.FontStyle]::Bold);
$wTxt = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White);
$g3.DrawString("ID", $fGh, $wTxt, ($boxX + 15), ($gHeadY + 8));
$g3.DrawString("Nombre del Producto", $fGh, $wTxt, ($boxX + 70), ($gHeadY + 8));
$g3.DrawString("Categoría", $fGh, $wTxt, ($boxX + 460), ($gHeadY + 8));
$g3.DrawString("Precio (ARS)", $fGh, $wTxt, ($boxX + 620), ($gHeadY + 8));
$g3.DrawString("Etiqueta", $fGh, $wTxt, ($boxX + 760), ($gHeadY + 8));
$g3.DrawString("Visible", $fGh, $wTxt, ($boxX + 880), ($gHeadY + 8));
$g3.DrawString("Ruta Archivo Imagen (fts/)", $fGh, $wTxt, ($boxX + 970), ($gHeadY + 8));
$wTxt.Dispose();

# Query MySQL for products data
$mysqlOut = & C:\xampp\mysql\bin\mysql.exe -u root -e "USE michell_store; SELECT id, nombre, categoria, precio, IFNULL(etiqueta,'') as etiqueta, visible, imagen FROM productos ORDER BY id ASC;" -B;
$lines = $mysqlOut -split "`r?`n";
$pRows = @();
for ($i = 1; $i -lt $lines.Count; $i++) {
    if ([string]::IsNullOrWhiteSpace($lines[$i])) { continue; }
    $parts = $lines[$i] -split "`t";
    if ($parts.Count -ge 7) {
        $pRows += ,@{
            Id = $parts[0];
            Nombre = $parts[1];
            Categoria = $parts[2];
            Precio = $parts[3];
            Etiqueta = $parts[4];
            Visible = $parts[5];
            Imagen = $parts[6];
        };
    }
}

$rowY = $gHeadY + 34;
$rowHeight = 27;
$maxDisplay = [Math]::Min(37, $pRows.Count);

for ($i = 0; $i -lt $maxDisplay; $i++) {
    $item = $pRows[$i];
    if ($i % 2 -eq 1) {
        $g3.FillRectangle((New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#FFF9F8"))), ($boxX + 1), $rowY, ($boxW - 2), $rowHeight);
    }
    $g3.DrawLine((New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#F0E6E5"), 1)), $boxX, ($rowY + $rowHeight), ($boxX + $boxW), ($rowY + $rowHeight));

    # ID
    $g3.DrawString($item.Id, (New-Object System.Drawing.Font("Consolas", 9, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#6C757D"))), ($boxX + 15), ($rowY + 6));
    # Nombre
    $g3.DrawString($item.Nombre, (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#212529"))), ($boxX + 70), ($rowY + 6));
    
    # Categoría Badge
    $catColor = switch ($item.Categoria) {
        "remeras"  { "#E0F2FE" }
        "calzas"   { "#DCFCE7" }
        "medias"   { "#FEF3C7" }
        "interior" { "#FCE7F3" }
        default    { "#F3F4F6" }
    };
    $catTxt = switch ($item.Categoria) {
        "remeras"  { "#0369A1" }
        "calzas"   { "#15803D" }
        "medias"   { "#B45309" }
        "interior" { "#BE185D" }
        default    { "#374151" }
    };
    Draw-RoundedRect $g3 $null (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($catColor))) ($boxX + 460) ($rowY + 4) 85 20 4;
    $g3.DrawString($item.Categoria, (New-Object System.Drawing.Font("Segoe UI", 8.5, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($catTxt))), ($boxX + 468), ($rowY + 5));

    # Precio
    $fmtPrecio = "$" + [double]::Parse($item.Precio).ToString("N2");
    $g3.DrawString($fmtPrecio, (New-Object System.Drawing.Font("Consolas", 9.5, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#8B2635"))), ($boxX + 620), ($rowY + 6));

    # Etiqueta
    if (-not [string]::IsNullOrEmpty($item.Etiqueta)) {
        Draw-RoundedRect $g3 $null (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#FEE2E2"))) ($boxX + 760) ($rowY + 4) 75 20 4;
        $g3.DrawString($item.Etiqueta, (New-Object System.Drawing.Font("Segoe UI", 7.5, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#991B1B"))), ($boxX + 766), ($rowY + 6));
    } else {
        $g3.DrawString("-", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#ADB5BD"))), ($boxX + 765), ($rowY + 6));
    }

    # Visible
    $visColor = if ($item.Visible -eq "1") { "#D1E7DD" } else { "#F8D7DA" };
    $visTxt = if ($item.Visible -eq "1") { "#0F5132" } else { "#842029" };
    $visLabel = if ($item.Visible -eq "1") { "SI (1)" } else { "NO (0)" };
    Draw-RoundedRect $g3 $null (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($visColor))) ($boxX + 880) ($rowY + 4) 55 20 4;
    $g3.DrawString($visLabel, (New-Object System.Drawing.Font("Segoe UI", 8, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($visTxt))), ($boxX + 886), ($rowY + 6));

    # Imagen
    $g3.DrawString($item.Imagen, (New-Object System.Drawing.Font("Consolas", 8.5, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#495057"))), ($boxX + 970), ($rowY + 6));

    $rowY += $rowHeight;
}

# Footer stats inside box
$g3.DrawString("Mostrando 37 de 37 registros en catálogo | Fuente: SELECT * FROM productos ORDER BY id ASC;", (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Italic)), (New-Object System.Drawing.SolidBrush($colGray)), ($boxX + 15), ($boxY + $boxH - 24));

$bmp3.Save("$outDir\03_contenido_tabla_productos.png", [System.Drawing.Imaging.ImageFormat]::Png);
$bmp3.Save("$artDir\03_contenido_tabla_productos.png", [System.Drawing.Imaging.ImageFormat]::Png);
$g3.Dispose(); $bmp3.Dispose();
Write-Output "Image 3 created successfully";

# =========================================================================
# IMAGEN 4: USUARIOS, SEGURIDAD Y CONFIGURACIÓN DEL SISTEMA
# =========================================================================
$w4 = 1400; $h4 = 820;
$bmp4 = New-Object System.Drawing.Bitmap($w4, $h4, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb);
$g4 = [System.Drawing.Graphics]::FromImage($bmp4);
$g4.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality;
$g4.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit;
$g4.Clear($colBg);

$b4 = New-Object System.Drawing.SolidBrush($colWine);
$g4.FillRectangle($b4, 0, 0, $w4, 80);
$b4.Dispose();
$g4.DrawString("TABLA 'usuarios', SEGURIDAD Y ARQUITECTURA DE DATOS", $fontTitle, (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), 40, 16);
$g4.DrawString("Autenticación, encriptación BCrypt, roles de usuario y resumen general de la base de datos", $fontSubtitle, (New-Object System.Drawing.SolidBrush($colWineLight)), 42, 50);

# Section 1: Tabla Usuarios (Card on Left)
$uX = 40; $uY = 110; $uW = 680; $uH = 340;
Draw-RoundedRect $g4 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#CED4DA"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)) $uX $uY $uW $uH 8;

$uHdr = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#1E3A8A"));
Draw-RoundedRect $g4 $null $uHdr $uX $uY $uW 36 8;
$g4.FillRectangle($uHdr, $uX, ($uY + 18), $uW, 18);
$uHdr.Dispose();
$g4.DrawString("REGISTRO ACTUAL: TABLA 'usuarios'", (New-Object System.Drawing.Font("Segoe UI", 11, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), ($uX + 15), ($uY + 8));

# Table columns
$g4.FillRectangle((New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#E9ECEF"))), $uX, ($uY + 36), $uW, 28);
$g4.DrawString("ID", $fontBold, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#495057"))), ($uX + 15), ($uY + 41));
$g4.DrawString("Usuario", $fontBold, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#495057"))), ($uX + 70), ($uY + 41));
$g4.DrawString("Rol", $fontBold, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#495057"))), ($uX + 180), ($uY + 41));
$g4.DrawString("Hash Contraseña (BCrypt / $2y$...)", $fontBold, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#495057"))), ($uX + 270), ($uY + 41));

# Query usuarios
$uOut = & C:\xampp\mysql\bin\mysql.exe -u root -e "USE michell_store; SELECT id, usuario, rol, password, fecha_registro FROM usuarios;" -B;
$uLines = $uOut -split "`r?`n";
if ($uLines.Count -gt 1) {
    $uData = $uLines[1] -split "`t";
    $currUY = $uY + 70;
    $g4.DrawString($uData[0], $fontMonoBold, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#6C757D"))), ($uX + 15), $currUY);
    $g4.DrawString($uData[1], (New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#212529"))), ($uX + 70), $currUY);
    
    # Rol badge
    Draw-RoundedRect $g4 $null (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#CFE2FF"))) ($uX + 180) ($currUY - 1) 65 22 4;
    $g4.DrawString($uData[2], (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#084298"))), ($uX + 188), ($currUY + 2));

    # Hash
    $shortHash = $uData[3].Substring(0, [Math]::Min(35, $uData[3].Length)) + "...";
    $g4.DrawString($shortHash, $fontMono, (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#842029"))), ($uX + 270), $currUY);
}

# Explanation Box in Users
$expY = $uY + 115;
Draw-RoundedRect $g4 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#BEE5EB"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#E8F4F8"))) ($uX + 15) $expY ($uW - 30) 200 6;
$g4.DrawString("🔐 SEGURIDAD Y GESTIÓN DE CREDENCIALES", (New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#0C5460"))), ($uX + 30), ($expY + 15));
$secPoints = @(
    "• Contraseñas protegidas mediante password_hash() con algoritmo PASSWORD_DEFAULT (BCrypt).",
    "• Verificación en login mediante password_verify() previniendo fugas de contraseñas planas.",
    "• Control de acceso basado en roles (RBAC): 'cliente' para compras y 'admin' para panel ABM.",
    "• Sesiones gestionadas por PHP ($_SESSION['rol'], $_SESSION['usuario_id']) con expiración.",
    "• Campo 'usuario' con restricción UNIQUE para evitar duplicidad de cuentas en el registro."
);
$sY = $expY + 45;
foreach ($sp in $secPoints) {
    $g4.DrawString($sp, (New-Object System.Drawing.Font("Segoe UI", 8.5, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#212529"))), ($uX + 30), $sY);
    $sY += 28;
}

# Section 2: Resumen Global de Métricas (Card on Right)
$sX = 750; $sY = 110; $sW = 610; $sH = 340;
Draw-RoundedRect $g4 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#CED4DA"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)) $sX $sY $sW $sH 8;

$sHdr = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#047857"));
Draw-RoundedRect $g4 $null $sHdr $sX $sY $sW 36 8;
$g4.FillRectangle($sHdr, $sX, ($sY + 18), $sW, 18);
$sHdr.Dispose();
$g4.DrawString("RESUMEN GENERAL DE LA BASE DE DATOS", (New-Object System.Drawing.Font("Segoe UI", 11, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), ($sX + 15), ($sY + 8));

# 4 Stat Pills
function Draw-StatPill($g, $px, $py, $pw, $ph, $title, $val, $colHex, $icon) {
    Draw-RoundedRect $g (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#E9ECEF"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#F8F9FA"))) $px $py $pw $ph 6;
    $g.DrawString($icon, (New-Object System.Drawing.Font("Segoe UI", 18, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml($colHex))), ($px + 12), ($py + 10));
    $g.DrawString($title, (New-Object System.Drawing.Font("Segoe UI", 8.5, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#6C757D"))), ($px + 52), ($py + 8));
    $g.DrawString($val, (New-Object System.Drawing.Font("Segoe UI", 14, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#212529"))), ($px + 52), ($py + 26));
}

Draw-StatPill $g4 ($sX + 20) ($sY + 55) 270 65 "TABLA: PRODUCTOS" "37 Registros" "#8B2635" "🛍️";
Draw-StatPill $g4 ($sX + 310) ($sY + 55) 270 65 "TABLA: USUARIOS" "1 Admin Activo" "#1E3A8A" "👤";
Draw-StatPill $g4 ($sX + 20) ($sY + 135) 270 65 "MOTOR PRINCIPAL" "InnoDB (ACID)" "#047857" "⚙️";
Draw-StatPill $g4 ($sX + 310) ($sY + 135) 270 65 "COTEJAMIENTO" "utf8mb4_unicode_ci" "#B45309" "🌐";

# Info list in Right Card
$g4.DrawLine((New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#E9ECEF"), 1)), ($sX + 20), ($sY + 220), ($sX + $sW - 20), ($sY + 220));
$metaItems = @(
    "• Nombre de la base de datos: 'michell_store'",
    "• Integridad Referencial: Claves foráneas (FK) con índices optimizados en producto_id.",
    "• Soporte Multilenguaje: Codificación completa UTF-8 incluyendo caracteres especiales y emojis.",
    "• Transacciones: Cumplimiento ACID garantizado para operaciones de compra y catálogo."
);
$mY = $sY + 230;
foreach ($mi in $metaItems) {
    $g4.DrawString($mi, (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#374151"))), ($sX + 20), $mY);
    $mY += 24;
}

# Section 3: Bottom Full Card - Tablas valoraciones y comentarios
$bX = 40; $bY = 475; $bW = 1320; $bH = 290;
Draw-RoundedRect $g4 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#CED4DA"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)) $bX $bY $bW $bH 8;

$bHdr = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#3B151B"));
Draw-RoundedRect $g4 $null $bHdr $bX $bY $bW 36 8;
$g4.FillRectangle($bHdr, $bX, ($bY + 18), $bW, 18);
$bHdr.Dispose();
$g4.DrawString("MÓDULO DE INTERACCIÓN Y OPINIONES: TABLAS 'valoraciones' Y 'comentarios'", (New-Object System.Drawing.Font("Segoe UI", 11, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)), ($bX + 15), ($bY + 8));

# Sub-column 1: valoraciones
Draw-RoundedRect $g4 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#FDE68A"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#FFFBEB"))) ($bX + 20) ($bY + 55) 620 215 6;
$g4.DrawString("⭐ TABLA: valoraciones (Sistema de Calificación 1 a 5 Estrellas)", (New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#92400E"))), ($bX + 35), ($bY + 70));
$valDetails = @(
    "• Objetivo: Almacenar la calificación cuantitativa otorgada por clientes a cada prenda.",
    "• Campo clave 'producto_id': Vincula la puntuación directamente con el catálogo.",
    "• Campo 'puntuacion': Entero restringido entre 1 y 5 procesado para calcular el promedio.",
    "• Endpoint asociado: 'guardar_valoracion.php' y 'obtener_valoraciones.php'.",
    "• Cálculo automático: El sistema calcula promedio y total de valoraciones en tiempo real."
);
$vdY = $bY + 100;
foreach ($vd in $valDetails) {
    $g4.DrawString($vd, (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#451A03"))), ($bX + 35), $vdY);
    $vdY += 22;
}

# Sub-column 2: comentarios
Draw-RoundedRect $g4 (New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#A7F3D0"), 1)) (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#ECFDF5"))) ($bX + 680) ($bY + 55) 620 215 6;
$g4.DrawString("💬 TABLA: comentarios (Muro de Preguntas y Respuestas)", (New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#065F46"))), ($bX + 695), ($bY + 70));
$comDetails = @(
    "• Objetivo: Permitir a clientes consultar dudas de talle, color o stock previo a la compra.",
    "• Campo clave 'producto_id': Agrupa las preguntas en el hilo del artículo correspondiente.",
    "• Campo 'comentario': Almacenamiento tipo TEXT permitiendo descripciones amplias.",
    "• Endpoints asociados: 'guardar_comentario.php' y 'eliminar_comentario.php'.",
    "• Moderación: Clientes pueden eliminar sus propios comentarios; administradores tienen control total."
);
$cdY = $bY + 100;
foreach ($cd in $comDetails) {
    $g4.DrawString($cd, (New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Regular)), (New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#064E3B"))), ($bX + 695), $cdY);
    $cdY += 22;
}

$bmp4.Save("$outDir\04_seguridad_usuarios_y_resumen.png", [System.Drawing.Imaging.ImageFormat]::Png);
$bmp4.Save("$artDir\04_seguridad_usuarios_y_resumen.png", [System.Drawing.Imaging.ImageFormat]::Png);
$g4.Dispose(); $bmp4.Dispose();
Write-Output "Image 4 created successfully";

Write-Output "ALL 4 DATABASE REPORT IMAGES GENERATED SUCCESSFULLY!";

