"""End-to-end API test for login, performance, and AI history."""
import json
import urllib.request
import urllib.error

BASE = "http://127.0.0.1:8001"
EMAIL = "xagent891@gmail.com"
NAME = "Test User"


def req(method, path, data=None, token=None):
    headers = {"Content-Type": "application/json"}
    if token:
        headers["Authorization"] = f"Bearer {token}"
    body = json.dumps(data).encode() if data else None
    r = urllib.request.Request(f"{BASE}{path}", data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(r, timeout=15) as resp:
            return resp.status, json.loads(resp.read().decode())
    except urllib.error.HTTPError as e:
        return e.code, json.loads(e.read().decode()) if e.read() else {"detail": str(e)}


def main():
    print("=== FastAPI health ===")
    code, data = req("GET", "/")
    print(f"GET / -> {code}: {data}")

    print("\n=== login-via-session (auto-provision) ===")
    code, data = req("POST", "/auth/login-via-session", {"email": EMAIL, "name": NAME})
    print(f"POST /auth/login-via-session -> {code}")
    if code != 200:
        print(data)
        return 1
    token = data["access_token"]
    print("JWT obtained:", token[:40] + "...")

    print("\n=== performance/stats ===")
    code, stats = req("GET", "/performance/stats", token=token)
    print(f"GET /performance/stats -> {code}")
    print(json.dumps(stats, indent=2))

    print("\n=== performance/dashboard-summary ===")
    code, dash = req("GET", "/performance/dashboard-summary", token=token)
    print(f"GET /performance/dashboard-summary -> {code}: {dash}")

    print("\n=== performance/insights ===")
    code, ins = req("GET", "/performance/insights", token=token)
    print(f"GET /performance/insights -> {code}: {ins}")

    print("\n=== ai/history (before) ===")
    code, hist = req("GET", "/ai/history", token=token)
    print(f"GET /ai/history -> {code}, count={len(hist) if isinstance(hist, list) else hist}")

    print("\n=== ai/ask (save chat) ===")
    code, ask = req("POST", "/ai/ask", {"prompt": "What is 2+2?", "model": "gemini"}, token=token)
    print(f"POST /ai/ask -> {code}")
    if code == 200:
        print("response preview:", (ask.get("response") or "")[:120])
    else:
        print(ask)

    print("\n=== ai/history (after) ===")
    code, hist2 = req("GET", "/ai/history", token=token)
    print(f"GET /ai/history -> {code}, count={len(hist2) if isinstance(hist2, list) else hist2}")
    if isinstance(hist2, list) and hist2:
        print("latest:", hist2[0].get("prompt", "")[:60])

    print("\n=== PHP login page ===")
    try:
        with urllib.request.urlopen("http://127.0.0.1:8080/templates/auth/login.php", timeout=10) as resp:
            print(f"PHP login page -> {resp.status}")
    except Exception as e:
        print(f"PHP login page FAILED: {e}")

    print("\nAll API checks done.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
