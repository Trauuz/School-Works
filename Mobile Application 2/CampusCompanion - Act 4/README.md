# Campus Companion - Laboratory Exercise 5

Storyboard-based UIKit continuation of the Campus Companion application.

## Student details

- Name: De Angel, Marc Ramon Emmanuel C.
- Section: BSIT WMA 3rd year - TW33
- Professor: Prof. Von Erick Mabitang

## Exercise 5 features

- Campus Announcements has a Refresh bar button alongside the existing Add button.
- Refresh makes an on-demand `URLSession` GET request to the named endpoint in `AnnouncementsViewController.swift`.
- The completion handler checks the network error, then HTTP status, then response data. Successful data is logged as raw JSON and decoded into `AnnouncementDTO` values.
- Decoded records replace the Core Data announcement collection. UIKit and Core Data updates happen on the main thread. The alert reports the response byte count after saving.
- `location` and `priority` are in the DTO, Core Data model, replacement logic, and table cell display. The original Add and Delete controls remain available.
- A failed request or decode leaves the existing local list unchanged and reports the error in the Xcode console.

## Endpoint status

The endpoint printed in the exercise, `https://raw.githubusercontent.com/feu-mad2/campus-companion-api/main/announcements.json`, returned HTTP 404 when checked on October 14, 2026. The app deliberately uses that exact course URL and does not fabricate a successful response. A working course feed is needed to capture the successful Refresh screenshots. If your professor supplies a corrected URL, change only `RemoteEndpoint.announcements` in `AnnouncementsViewController.swift`.

## Screenshots for submission

1. Campus Announcements before tapping Refresh.
2. The `Server Reached` alert after a successful Refresh.
3. The Xcode console showing the raw JSON response.
4. Campus Announcements after Refresh, visibly showing Location and Priority Level.

The PDF is prepared with a plain `SCREENSHOT OUTPUT` section at its end so these can be added later.
