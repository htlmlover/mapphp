<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté, le rediriger vers la page de compte
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: compte.php");
    exit();
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Informations de connexion à la base de données
    $servername = "mysql-nolannlev.alwaysdata.net";
    $username = "nolannlev_1";
    $password = "tpphpmdp";
    $dbname = "nolannlev_tliens";

    // Récupérer les informations du formulaire
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];

    // Créer une connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Préparer la requête SQL pour récupérer l'utilisateur correspondant au nom d'utilisateur donné
    $sql = "SELECT * FROM tpwd WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username_input);
    $stmt->execute();

    // Récupérer le résultat de la requête
    $result = $stmt->get_result();

    // Vérifier si un utilisateur correspondant a été trouvé
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['passwd'];

        // Vérifier si le mot de passe correspond
        if (password_verify($password_input, $hashed_password)) {
            // Authentification réussie, ouvrir une session
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username_input;
            header("Location: carte.php"); // Rediriger vers la page de compte par exemple
            exit();
        } else {
            // Mot de passe incorrect, afficher un message d'erreur
            $error_message = "Mot de passe incorrect.";
        }
    } else {
        // Aucun utilisateur correspondant trouvé, afficher un message d'erreur
        $error_message = "Aucun utilisateur correspondant trouvé.";
    }

    // Fermer la connexion à la base de données
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
   
</head>

<body>

            <div class="login-box">
 
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <center><h1 >Connexion</h1></center>
                        <div class="user-box">
                            <input type="text" name="username" required>
                            <label>Username</label>
                        </div>
                        <div class="user-box">
                            <input type="password" name="password" required>
                            <label>Password</label>
                        </div ><center>

                        <input type="submit" value="Se connecter" class="btn">
                        
                        <br>
                        <a href="inscription.php">
                                S'INSCRIRE
                            <span></span>
                            </a></center>
                                
                        </center>
                    </form>
                    </div>
           
        </form>
        <?php if(isset($error_message)) { ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php } ?>
    </div>
</body>
<style>
.login-box h1{
  color: #fff;
}
.login-box {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 400px;
    padding: 40px;
    transform: translate(-50%, -50%);
    background: rgba(24, 20, 20, 0.987);
    box-sizing: border-box;
    box-shadow: 0 15px 25px rgba(0,0,0,.6);
    border-radius: 10px;
  }
  
  .login-box .user-box {
    position: relative;
  }
  
  .login-box .user-box input {
    width: 100%;
    padding: 10px 0;
    font-size: 16px;
    color: #fff;
    margin-bottom: 30px;
    border: none;
    border-bottom: 1px solid #fff;
    outline: none;
    background: transparent;
  }
  
  .login-box .user-box label {
    position: absolute;
    top: 0;
    left: 0;
    padding: 10px 0;
    font-size: 16px;
    color: #fff;
    pointer-events: none;
    transition: .5s;
  }
  
  .login-box .user-box input:focus ~ label,
  .login-box .user-box input:valid ~ label {
    top: -20px;
    left: 0;
    color: #bdb8b8;
    font-size: 12px;
  }
  
  .login-box form .btn {
    position: relative;
    display: inline-block;
    padding: 10px 20px;
    color: #fff;
    font-size: 16px;
    text-decoration: none;
    text-transform: uppercase;
    overflow: hidden;
    transition: .5s;
    margin-top: 40px;
    letter-spacing: 4px;
    background-color:rgba(24, 20, 20, 0.987);
    border:rgba(24, 20, 20, 0.987);
  }
  
  .login-box .btn:hover {
    background: yellow;
    color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 5px #03f40f,
                0 0 25px #03f40f,
                0 0 50px #03f40f,
                0 0 100px #03f40f;
  }
  
  .login-box form a {
  position: relative;
  display: inline-block;
  padding: 10px 20px;
  color: #ffffff;
  font-size: 16px;
  text-decoration: none;
  text-transform: uppercase;
  overflow: hidden;
  transition: .5s;
  margin-top: 40px;
  letter-spacing: 4px
}

.login-box a:hover {
  background: #03f40f;
  color: #fff;
  border-radius: 5px;
  box-shadow: 0 0 5px #03f40f,
              0 0 25px #03f40f,
              0 0 50px #03f40f,
              0 0 100px #03f40f;
}

.login-box a span {
  position: absolute;
  display: block;
}

@keyframes btn-anim1 {
  0% {
    left: -100%;
  }

  50%,100% {
    left: 100%;
  }
}

.login-box a span:nth-child(1) {
  bottom: 2px;
  left: -100%;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg, transparent, #03f40f);
  animation: btn-anim1 2s linear infinite;
}
</style>
</html>
