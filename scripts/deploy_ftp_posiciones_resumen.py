"""Sube los archivos cambiados a Ferozo por FTP_TLS y verifica con MD5.

Pensado para deploys chicos del proyecto Yii de veteranos.ar.

Uso:
    python scripts/deploy_ftp_posiciones_resumen.py

Credenciales:
- Por default se leen de datos.ftp.txt en la raiz del proyecto
  (formato key=value, ya esta en .gitignore).
- Cualquier valor se puede pisar por env var:
    FTP_HOST, FTP_USER, FTP_PASSWORD, FTP_REMOTE_ROOT

Filenames:
- FILES define que se sube, relativo a la raiz del proyecto.
- Editar la lista para reusar el script en otros deploys.
"""
import ftplib
import hashlib
import os
import sys

LOCAL_BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CREDS_FILE = os.path.join(LOCAL_BASE, "datos.ftp.txt")

# Archivos a subir, relativos a LOCAL_BASE.
FILES = [
    "protected/controllers/PosicionesController.php",
    "protected/views/posiciones/resumenFechaPdf.php",
]


def load_credentials():
    creds = {}
    if os.path.isfile(CREDS_FILE):
        with open(CREDS_FILE, "r", encoding="utf-8") as fh:
            for line in fh:
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                key, _, value = line.partition("=")
                creds[key.strip()] = value.strip()

    return {
        "host": os.environ.get("FTP_HOST") or creds.get("Host", ""),
        "user": os.environ.get("FTP_USER") or creds.get("UsuarioFTP", ""),
        "password": os.environ.get("FTP_PASSWORD") or creds.get("ClaveFTP", ""),
        "root": os.environ.get("FTP_REMOTE_ROOT") or creds.get("Carpetadestino", "/"),
    }


def md5(path):
    h = hashlib.md5()
    with open(path, "rb") as fh:
        for chunk in iter(lambda: fh.read(65536), b""):
            h.update(chunk)
    return h.hexdigest()


def ensure_dir(ftp, path):
    parts = [p for p in path.split("/") if p]
    cur = ""
    for p in parts:
        cur = (cur + "/" + p) if cur else ("/" + p)
        try:
            ftp.cwd(cur)
        except ftplib.error_perm:
            ftp.mkd(cur)
            ftp.cwd(cur)


def main():
    creds = load_credentials()
    if not (creds["host"] and creds["user"] and creds["password"]):
        sys.stderr.write(
            "Faltan credenciales FTP. Definilas en datos.ftp.txt "
            "(Host/UsuarioFTP/ClaveFTP) o via env vars "
            "FTP_HOST/FTP_USER/FTP_PASSWORD.\n"
        )
        sys.exit(1)

    for rel in FILES:
        local = os.path.join(LOCAL_BASE, rel)
        if not os.path.isfile(local):
            sys.stderr.write("MISSING %s\n" % local)
            sys.exit(1)

    ftps = ftplib.FTP_TLS(creds["host"])
    ftps.login(creds["user"], creds["password"])
    ftps.prot_p()
    print("connected:", creds["host"])

    root = creds["root"].rstrip("/") or ""
    for rel in FILES:
        local = os.path.join(LOCAL_BASE, rel)
        remote = (root + "/" + rel) if root else ("/" + rel)
        remote_dir = os.path.dirname(remote).replace("\\", "/")
        ensure_dir(ftps, remote_dir)
        ftps.cwd(remote_dir)
        with open(local, "rb") as fh:
            ftps.storbinary("STOR " + os.path.basename(remote), fh)
        size_remote = ftps.size(os.path.basename(remote))
        local_size = os.path.getsize(local)
        local_md5 = md5(local)
        print("OK %s  local=%dB  remote=%dB  md5=%s" % (rel, local_size, size_remote, local_md5))
        if local_size != size_remote:
            sys.stderr.write("  !! SIZE MISMATCH on %s\n" % rel)

    ftps.quit()
    print("done")


if __name__ == "__main__":
    main()
