<?php
try {
    $mysqli = new mysqli('localhost', 'root', '', 'agropredi');
    $mysqli->query('DROP TABLE IF EXISTS diagnostics');
    echo "✅ Table diagnostics supprimée\n";
    $mysqli->close();
} catch(Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
