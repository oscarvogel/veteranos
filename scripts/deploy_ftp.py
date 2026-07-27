"""Sube archivos del proyecto a Ferozo por FTP_TLS y verifica con MD5.

Pensado para deploys chicos (un par de archivos) del proyecto Yii de
veteranos.ar. Si en algun momento hay que hacer deploys grandes, conviene
migrar a rsync/scp o a un pipeline con GitHub Actions.

Uso:
    python scripts/deploy_ftp.py ruta/relativa/al/archivo1 [archivo2 ...]

Las rutas son relativas a la raiz del proyecto (donde esta este script
un nivel arriba). Si no se pasa ningun archivo, se muestra esta ayuda.

Ejemplos:
    python scripts/deploy_ftp.py protected/controllers/SiteController.php
    python scripts/deploy_ftp.py \\
        protected/controllers/SiteController.php \\
        protected/views/site/index.php

Credenciales:
- Por default se leen de datos.ftp.txt en la raiz del proyecto
  (formato key=value, ya esta en .gitignore).
- Cualquier valor se puede pisar por env var:
    FTP_HOST, FTP_USER, FTP_PASSWORD, FTP_REMOTE_ROOT

Si la variable de entorno FTP_DRY_RUN esta en 1, el script imprime que
subiria sin conectar al server. Util para revisar antes de tocar prod.
"""
import ftplib
import hashlib
import os
import sys

LOCAL_BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CREDS_FILE = os.path.join(LOCAL_BASE, "datos.ftp.txt")


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


def upload(ftps, root, rel):
    rel = rel.replace("\\", "/")
    local = os.path.join(LOCAL_BASE, rel)
    if not os.path.isfile(local):
        sys.stderr.write("MISSING %s\n" % local)
        return False

    remote = (root + "/" + rel) if root else ("/" + rel)
    remote = remote.replace("\\", "/")
    remote_dir = os.path.dirname(remote)
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
    return True


def main(argv):
    if not argv:
        sys.stderr.write(__doc__)
        sys.exit(1)

    creds = load_credentials()
    if not (creds["host"] and creds["user"] and creds["password"]):
        sys.stderr.write(
            "Faltan credenciales FTP. Definilas en datos.ftp.txt "
            "(Host/UsuarioFTP/ClaveFTP) o via env vars "
            "FTP_HOST/FTP_USER/FTP_PASSWORD.\n"
        )
        sys.exit(1)

    root = creds["root"].rstrip("/") or ""
    dry_run = os.environ.get("FTP_DRY_RUN") == "1"

    if dry_run:
        for rel in argv:
            rel = rel.replace("\\", "/")
            local = os.path.join(LOCAL_BASE, rel)
            if not os.path.isfile(local):
                sys.stderr.write("MISSING %s\n" % local)
                continue
            remote = (root + "/" + rel) if root else ("/" + rel)
            remote = remote.replace("\\", "/")
            print("DRY %s -> %s  size=%dB" % (rel, remote, os.path.getsize(local)))
        return 0

    ftps = ftplib.FTP_TLS(creds["host"])
    ftps.login(creds["user"], creds["password"])
    ftps.prot_p()
    print("connected:", creds["host"])

    failed = 0
    for rel in argv:
        if not upload(ftps, root, rel):
            failed += 1

    ftps.quit()
    if failed:
        print("done with %d failure(s)" % failed)
    else:
        print("done")
    return 0 if failed == 0 else 1


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
