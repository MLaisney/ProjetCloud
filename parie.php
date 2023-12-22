<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <title>Parie en ligne</title>
</head>

<body>
    <div style="padding-top:10px">
        <a href="index.php" class="bouton-nav" style="margin-right:5px">Retour</a>
    </div>

    <div class="mid">
        <?php
        require('../config.php');

        $MiseGlobal = 0;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'resultat') === 0) {
                    $matchId = substr($key, strlen('resultat'));
                    $resultat = htmlspecialchars($value);

                    $choixUtilisateur = 2;
                    if ($resultat === 'equipe1Gagne') {
                        $choixUtilisateur = 1;
                    } elseif ($resultat === 'equipe2Gagne') {
                        $choixUtilisateur = 3;
                    }

                    $MiseAJour = "UPDATE matchs SET choix_utilisateur = ? WHERE id = ?";
                    $MiseAJourRequete = $base->prepare($MiseAJour);
                    $MiseAJourRequete->execute([$choixUtilisateur, $matchId]);
                } elseif ($key === 'MiseGlobal') {
                    $mise = floatval(htmlspecialchars($value));

                    $MiseAJourMise = "UPDATE info SET mise = ? WHERE id = 1";
                    $MiseAJourMiseRequete = $base->prepare($MiseAJourMise);
                    $MiseAJourMiseRequete->execute([$mise]);
                }
            }
        }

        $getMiseQuery = "SELECT mise FROM info WHERE id = 1";
        $requeteMise = $base->prepare($getMiseQuery);
        $requeteMise->execute();
        $infoRow = $requeteMise->fetch();
        if ($infoRow) {
            $MiseGlobal = $infoRow["mise"];
        }

        $requeteMatchs = "SELECT * FROM matchs";
        $resultatMatchs = $base->prepare($requeteMatchs);
        $resultatMatchs->execute();
        echo "<br>";
        echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
        echo "<table>";
        echo "<tr><th>Equipe 1</th><th>Equipe 2</th><th>Cote Equipe 1</th><th>Cote Match Nul</th><th>Cote Equipe 2</th><th>Résultat</th></tr>";

        $CoteTotal = 1;

        while ($row = $resultatMatchs->fetch()) {

            $requeteEquipe1 = "SELECT * FROM equipe WHERE id = ?";
            $resultatEquipe1 = $base->prepare($requeteEquipe1);
            $resultatEquipe1->execute([$row["equipe_1_id"]]);
            $equipe1 = $resultatEquipe1->fetch();


            $requeteEquipe2 = "SELECT * FROM equipe WHERE id = ?";
            $resultatEquipe2 = $base->prepare($requeteEquipe2);
            $resultatEquipe2->execute([$row["equipe_2_id"]]);
            $equipe2 = $resultatEquipe2->fetch();

            echo "<tr>";
            echo "<td><img src='../image/" . $equipe1["url_image"] . "'></td>";
            echo "<td><img src='../image/" . $equipe2["url_image"] . "'></td>";
            echo "<td>" . $row["cote_victoire"] . "</td>";
            echo "<td>" . $row["cote_nul"] . "</td>";
            echo "<td>" . $row["cote_defaite"] . "</td>";

            $ResultatParDéfaut = 'matchNul';

            $requeteChoix = "SELECT choix_utilisateur FROM matchs WHERE id = ?";
            $resultatChoix = $base->prepare($requeteChoix);
            $resultatChoix->execute([$row["id"]]);
            $choixUtilisateurRow = $resultatChoix->fetch();
            if ($choixUtilisateurRow) {
                $ChoixPrésedent = $choixUtilisateurRow["choix_utilisateur"];
                if ($ChoixPrésedent == 1) {
                    $ResultatParDéfaut = 'equipe1Gagne';
                } elseif ($ChoixPrésedent == 3) {
                    $ResultatParDéfaut = 'equipe2Gagne';
                }
            }

            echo "<td>
        <input type='radio' id='equipe1Gagne" . $row["id"] . "' name='resultat" . $row["id"] . "' value='equipe1Gagne'" . ($ResultatParDéfaut === 'equipe1Gagne' ? ' checked' : '') . ">
        <label for='equipe1Gagne" . $row["id"] . "'>Equipe 1 Gagne</label>

        <input type='radio' id='matchNul" . $row["id"] . "' name='resultat" . $row["id"] . "' value='matchNul'" . ($ResultatParDéfaut === 'matchNul' ? ' checked' : '') . ">
        <label for='matchNul" . $row["id"] . "'>Match Nul</label>

        <input type='radio' id='equipe2Gagne" . $row["id"] . "' name='resultat" . $row["id"] . "' value='equipe2Gagne'" . ($ResultatParDéfaut === 'equipe2Gagne' ? ' checked' : '') . ">
        <label for='equipe2Gagne" . $row["id"] . "'>Equipe 2 Gagne</label>
    </td>";

            $CoteTotal *= ($resultat === 'matchNul') ? $row["cote_nul"] : (($resultat === 'equipe1Gagne') ? $row["cote_victoire"] : $row["cote_defaite"]);

            echo "</tr>";
        }

        echo "</table>";

        echo "<div class='boite_centre'>";

        echo "<label for='MiseGlobal'>Mise Globale:</label>";
        echo "<input type='text' name='MiseGlobal' value='" . $MiseGlobal . "'>";


        echo "<p>Total des Cotes: " . number_format($CoteTotal, 2) . "</p>";

        echo "Gain avec cote : " . number_format($MiseGlobal * $CoteTotal, 2)   . "€ <br>";

        echo "<div class='boutons'>";
        echo "<button type='submit' class='bouton-nav'>Valider</button>";
        echo "</div>";
        echo "</form>";

        ?>
    <br>
        <a href="../admin/choix_win.php" class="bouton-nav" style="margin-right:5px">Prochaine étape</a>
    </div>
    
</body>

</html>