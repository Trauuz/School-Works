import UIKit
import CoreData

private enum RemoteEndpoint {
    static let announcements = URL(
        string: "https://raw.githubusercontent.com/feu-mad2/campus-companion-api/main/announcements.json"
    )!
}

class AnnouncementsViewController: UITableViewController {
    private var announcements: [CampusAnnouncement] = []
    private var selectedAnnouncement: CampusAnnouncement?
    private let seededKey = "campusAnnouncementsSeeded"

    private var context: NSManagedObjectContext {
        (UIApplication.shared.delegate as! AppDelegate).persistentContainer.viewContext
    }

    override func viewDidLoad() {
        super.viewDidLoad()
        title = "Campus Announcements"
        tableView.rowHeight = 100
        seedInitialAnnouncementsIfNeeded()
        loadAnnouncements()
    }

    @IBAction private func refreshTapped(_ sender: UIBarButtonItem) {
        fetchAnnouncementsFromServer()
    }

    private func fetchAnnouncementsFromServer() {
        let task = URLSession.shared.dataTask(with: RemoteEndpoint.announcements) { [weak self] data, response, error in
            if let error = error {
                print("Network error: \(error.localizedDescription)")
                return
            }

            guard let httpResponse = response as? HTTPURLResponse,
                  (200...299).contains(httpResponse.statusCode) else {
                let status = (response as? HTTPURLResponse)?.statusCode ?? -1
                print("Unexpected server response: HTTP \(status).")
                return
            }

            guard let data = data else {
                print("Server returned no announcement data.")
                return
            }

            print("Reached network handler with \(data.count) bytes.")
            if let rawJSON = String(data: data, encoding: .utf8) {
                print("Raw JSON response:\n\(rawJSON)")
            }

            do {
                let decodedAnnouncements = try JSONDecoder().decode([AnnouncementDTO].self, from: data)
                DispatchQueue.main.async {
                    guard let self else { return }
                    do {
                        try self.replaceAnnouncements(with: decodedAnnouncements)
                        self.showNetworkConfirmationAlert(byteCount: data.count)
                    } catch {
                        self.context.rollback()
                        self.showPersistenceError(error)
                    }
                }
            } catch {
                print("Decoding error: \(error)")
            }
        }
        task.resume()
    }

    private func replaceAnnouncements(with dtos: [AnnouncementDTO]) throws {
        let request: NSFetchRequest<CampusAnnouncement> = CampusAnnouncement.fetchRequest()
        let existing = try context.fetch(request)
        for item in existing { context.delete(item) }

        for dto in dtos {
            let announcement = CampusAnnouncement(context: context)
            announcement.title = dto.title
            announcement.date = dto.date
            announcement.category = dto.category
            announcement.location = dto.location ?? "Not specified"
            announcement.priority = dto.priority ?? "Normal"
        }

        try context.save()
        loadAnnouncements()
    }

    private func showNetworkConfirmationAlert(byteCount: Int) {
        let alert = UIAlertController(
            title: "Server Reached",
            message: "Received \(byteCount) bytes from the announcements endpoint.",
            preferredStyle: .alert
        )
        alert.addAction(UIAlertAction(title: "OK", style: .default))
        present(alert, animated: true)
    }

    private func seedInitialAnnouncementsIfNeeded() {
        let request: NSFetchRequest<CampusAnnouncement> = CampusAnnouncement.fetchRequest()
        guard let existingCount = try? context.count(for: request) else { return }
        if existingCount > 0 {
            UserDefaults.standard.set(true, forKey: seededKey)
            return
        }
        guard !UserDefaults.standard.bool(forKey: seededKey) else { return }

        let initialEntries: [(title: String, date: String, category: String)] = [
            ("Enrollment Period Extended", "August 3, 2026", "Registrar"),
            ("Welcome Week Activities", "August 5, 2026", "Student Life"),
            ("Library Orientation Schedule", "August 7, 2026", "Library"),
            ("Scholarship Application Deadline", "August 10, 2026", "Scholarships"),
            ("Campus Clean-Up Drive", "August 12, 2026", "Community"),
            ("Intramural Sports Registration", "August 15, 2026", "Sports")
        ]

        for entry in initialEntries {
            let announcement = CampusAnnouncement(context: context)
            announcement.title = entry.title
            announcement.date = entry.date
            announcement.category = entry.category
            announcement.location = "Campus"
            announcement.priority = ["Enrollment Period Extended", "Scholarship Application Deadline"]
                .contains(entry.title) ? "Urgent" : "Normal"
        }

        do {
            try context.save()
            UserDefaults.standard.set(true, forKey: seededKey)
        } catch {
            context.rollback()
            showPersistenceError(error)
        }
    }

    private func loadAnnouncements() {
        let request: NSFetchRequest<CampusAnnouncement> = CampusAnnouncement.fetchRequest()
        request.sortDescriptors = [NSSortDescriptor(key: "date", ascending: true)]

        do {
            let fetched = try context.fetch(request)
            let dateFormatter = DateFormatter()
            dateFormatter.locale = Locale(identifier: "en_US_POSIX")
            dateFormatter.dateFormat = "MMMM d, yyyy"
            let isoDateFormatter = DateFormatter()
            isoDateFormatter.locale = Locale(identifier: "en_US_POSIX")
            isoDateFormatter.dateFormat = "yyyy-MM-dd"
            func parsedDate(_ raw: String?) -> Date {
                let value = raw ?? ""
                return dateFormatter.date(from: value)
                    ?? isoDateFormatter.date(from: value)
                    ?? .distantFuture
            }
            announcements = fetched.sorted { first, second in
                let firstDate = parsedDate(first.date)
                let secondDate = parsedDate(second.date)
                return firstDate == secondDate
                    ? (first.title ?? "").localizedStandardCompare(second.title ?? "") == .orderedAscending
                    : firstDate < secondDate
            }
            tableView.reloadData()
        } catch {
            showPersistenceError(error)
        }
    }

    @IBAction private func addAnnouncementTapped(_ sender: UIBarButtonItem) {
        let alert = UIAlertController(
            title: "New Announcement",
            message: "Enter a title and category.",
            preferredStyle: .alert
        )
        alert.addTextField { $0.placeholder = "Title" }
        alert.addTextField { $0.placeholder = "Category" }

        let saveAction = UIAlertAction(title: "Save", style: .default) { [weak self, weak alert] _ in
            guard let self,
                  let fields = alert?.textFields, fields.count == 2 else { return }
            let titleText = (fields[0].text ?? "").trimmingCharacters(in: .whitespacesAndNewlines)
            let categoryText = (fields[1].text ?? "").trimmingCharacters(in: .whitespacesAndNewlines)
            guard !titleText.isEmpty, !categoryText.isEmpty else { return }

            let newAnnouncement = CampusAnnouncement(context: self.context)
            newAnnouncement.title = titleText
            newAnnouncement.date = DateFormatter.localizedString(
                from: Date(), dateStyle: .long, timeStyle: .none
            )
            newAnnouncement.category = categoryText
            newAnnouncement.location = "Campus"
            newAnnouncement.priority = "Normal"
            do {
                try self.context.save()
                self.loadAnnouncements()
            } catch {
                self.context.rollback()
                self.showPersistenceError(error)
            }
        }
        alert.addAction(saveAction)
        alert.addAction(UIAlertAction(title: "Cancel", style: .cancel))
        present(alert, animated: true)
    }

    override func tableView(
        _ tableView: UITableView,
        commit editingStyle: UITableViewCell.EditingStyle,
        forRowAt indexPath: IndexPath
    ) {
        guard editingStyle == .delete else { return }
        let announcement = announcements[indexPath.row]
        context.delete(announcement)
        do {
            try context.save()
            announcements.remove(at: indexPath.row)
            tableView.deleteRows(at: [indexPath], with: .automatic)
        } catch {
            context.rollback()
            showPersistenceError(error)
        }
    }

    private func showPersistenceError(_ error: Error) {
        let alert = UIAlertController(
            title: "Unable to Save Announcements",
            message: error.localizedDescription,
            preferredStyle: .alert
        )
        alert.addAction(UIAlertAction(title: "OK", style: .default))
        present(alert, animated: true)
    }

    override func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        announcements.count
    }

    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        guard let cell = tableView.dequeueReusableCell(
            withIdentifier: "AnnouncementCell",
            for: indexPath
        ) as? AnnouncementCell else {
            fatalError("AnnouncementCell is not configured in Main.storyboard")
        }

        cell.configure(with: announcements[indexPath.row])
        return cell
    }

    override func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        tableView.deselectRow(at: indexPath, animated: true)
        selectedAnnouncement = announcements[indexPath.row]
        performSegue(withIdentifier: "ShowAnnouncementDetailSegue", sender: self)
    }

    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        guard segue.identifier == "ShowAnnouncementDetailSegue",
              let destination = segue.destination as? DetailViewController,
              let selectedAnnouncement else {
            return
        }

        destination.announcement = selectedAnnouncement
    }
}

extension CampusAnnouncement {
    var postedBy: String {
        switch title {
        case "Enrollment Period Extended": return "Office of the Registrar"
        case "Welcome Week Activities": return "Student Affairs Office"
        case "Library Orientation Schedule": return "University Library"
        case "Scholarship Application Deadline": return "Scholarship Office"
        case "Campus Clean-Up Drive": return "Student Council"
        case "Intramural Sports Registration": return "Athletics Office"
        default: return "Campus Office"
        }
    }
}
